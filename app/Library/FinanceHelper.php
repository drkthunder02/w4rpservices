<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library;

use DB;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Corporation\CorpJournal;
use App\Models\User\UserToCorporation;
use App\Models\Finances\PlayerDonationJournal;

use App\Library\Esi;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class FinanceHelper {

    public function GetWalletJournal($division, $charId) {
        //Get hte ESI token for the corporation to add new wallet journals into the database
        $token = DB::table('EsiTokens')->where('character_id', $charId)->get();

        //Reference to see if the character is in our look up table for corporations and characters
        $corpId = $this->GetCharCorp($charId);

        //Disable all caching by setting the NullCache as the preferred cache handler.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Create an ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        //Create the esi class varialble
        $esi = new Eseye($authentication);
        
        //Set our current page to 1 which is the one we are starting on.
        $currentPage = 1;
        //Set our default total pages to 1 in case our try section fails out.
        $totalPages = 1;

        //If more than one page is found, decode the first set of wallet entries, then call for the next pages
        do {
            //Call the first page of the wallet journal, as we are always going to get at least one page.
            //If we have more pages, then we will continue through the while loop.
            try {
                $journals = $esi->page($currentPage)
                                ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                    'corporation_id' => $corpId,
                    'division'  => $division,
                ]);
            } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
                return $e->getEsiResponse();
            }

            //Set the total pages we need to cycle through.
            $totalPages = $journals->pages;
            //Decode the wallet from json into an array
            $wallet = json_decode($journals->raw, true);
            //For each journal entry, attempt to store it in the database.
            //The PutWalletJournal function checks to see if it's already in the database.
            foreach($wallet as $entry) {
                if($entry['amount'] > 0) {
                    if($entry['ref_type'] == 'brokers_fee' || 
                       $entry['ref_type'] == 'reprocessing_tax' || 
                       $entry['ref_type'] == 'jumpgate_fee' || 
                       $entry['ref_type'] == 'player_donation') {
                        $this->PutWalletJournal($entry, $corpId, $division);
                    }
                }
                
            }
            
            //Increment the current page we are on.
            $currentPage++;
        //Continue looping through the do while loop until the current page is greater than or equal to the total pages.
        } while ($currentPage < $totalPages);
    }

    /**
     * Returns the corporation a character is in if found in the lookup table, otherwise,
     * adds the character to the lookup table, and returns the corporation id
     * 
     * @param charId
     * @return corpId
     */
    private function GetCharCorp($charId) {
        //Check for the character the user_to_corporation table
        //$found = DB::table('user_to_corporation')->where('character_id', $charId)->get();
        $found = UserToCorporation::where('character_id', $charId)->get(['corporation_id']);
        //If we don't find the character in the table, then let's retrieve the information from ESI
        if(!isset($found[0]->corporation_id)) {
            //Get the configuration for ESI from the environmental variables
            $config = config('esi');
            //Setup a new ESI container
            $esi = new Eseye();
            //Try to get the character information, then the corporation information
            try {
                $character = $esi->invoke('get', '/characters/{character_id}/', [
                    'character_id' => $charId,
                ]);
                $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $character->corporation_id,
                ]);
            } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
                return $e->getEsiResponse();
            }

            //Save all of the data to the database
            $char = new UserToCorporation;
            $char->character_id = $charId;
            $char->character_name = $character->name;
            $char->corporation_id = $character->corporation_id;
            $char->corporation_name = $corporation->name;
            $char->save();
            //Return the corporation_id which is what the calling function is looking for
            return $character->corporation_id;
        } else {
            //Return the corporation_id if it was found in the database as it is what the calling function is looking for
            return $found[0]->corporation_id;
        }
    }

    private function PutWalletJournal($journal, $corpId, $division) {
        //Create ESI Helper class
        $esiHelper = new Esi;
        $date = $esiHelper->DecodeDate($journal['date']);

        if($journal['ref_type'] == 'player_donation') {
            //if we don't find the journal entry, add the journal entry to the database
            if(!PlayerDonationJournal::where(['id' => $journal['id']])->exists()) {
                $entry = new CorpJournal;
                $entry->id = $journal['id'];
                $entry->corporation_id = $corpId;
                $entry->division = $division;
                if(isset($journal['amount'])) {
                    $entry->amount = $journal['amount'];
                }
                if(isset($journal['balance'])) {
                    $entry->balance = $journal['balance'];
                }
                if(isset($journal['context_id'])) {
                    $entry->context_id = $journal['context_id'];
                }
                if(isset($journal['context_id_type'])) {
                    $entry->context_id_type = $journal['context_id_type'];
                }
                $entry->date = $date;
                $entry->description = $journal['description'];
                if(isset($journal['first_party_id'])) {
                    $entry->first_party_id = $journal['first_party_id'];
                }
                if(isset($journal['reason'])) {
                    $entry->reason = $journal['reason'];
                }
                $entry->ref_type = $journal['ref_type'];
                if(isset($journal['second_party_id'])) {
                    $entry->second_party_id = $journal['second_party_id'];
                }
                if(isset($journal['tax'])) {
                    $entry->tax = $journal['tax'];
                }
                if(isset($journal['tax_receiver_id'])) {
                    $entry->tax_receiver_id = $journal['tax_receiver_id'];
                }
                $entry->save();
            }   
        } else {
            //if we don't find the journal entry, add the journal entry to the database
            if(!CorpJournal::where(['id' => $journal['id']])->exists()) {
                $entry = new CorpJournal;
                $entry->id = $journal['id'];
                $entry->corporation_id = $corpId;
                $entry->division = $division;
                if(isset($journal['amount'])) {
                    $entry->amount = $journal['amount'];
                }
                if(isset($journal['balance'])) {
                    $entry->balance = $journal['balance'];
                }
                if(isset($journal['context_id'])) {
                    $entry->context_id = $journal['context_id'];
                }
                if(isset($journal['context_id_type'])) {
                    $entry->context_id_type = $journal['context_id_type'];
                }
                $entry->date = $date;
                $entry->description = $journal['description'];
                if(isset($journal['first_party_id'])) {
                    $entry->first_party_id = $journal['first_party_id'];
                }
                if(isset($journal['reason'])) {
                    $entry->reason = $journal['reason'];
                }
                $entry->ref_type = $journal['ref_type'];
                if(isset($journal['second_party_id'])) {
                    $entry->second_party_id = $journal['second_party_id'];
                }
                if(isset($journal['tax'])) {
                    $entry->tax = $journal['tax'];
                }
                if(isset($journal['tax_receiver_id'])) {
                    $entry->tax_receiver_id = $journal['tax_receiver_id'];
                }
                $entry->save();
            }    
        }
    }

}

?>