<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Finances\Helper;

use DB;

use App\Models\User\UserToCorporation;
use App\Models\Esi\EsiToken;

use App\Library\Esi;
use App\Library\Finances\MarketTax;
use App\Library\Finances\PlayerDonation;
use App\Library\Finances\ReprocessingTax;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class FinanceHelper {

    public function GetWalletJournal($division, $charId) {
        //Get hte ESI token for the corporation to add new wallet journals into the database
        $token = EsiToken::where(['character_id' => $charId])->get();

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
            'refresh_token' => $token->refresh_token,
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
                    if($entry['ref_type'] == 'brokers_fee') {
                        $market = new MarketTax();
                        $market->InsertMarketTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'reprocessing_tax') {
                        $reprocessing = new ReprocessingTax();
                        $reprocessing->InsertReprocessingTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'jumpgate_fee') {
                        $jb = new JumpBridgeTax();
                        $jb->InsertJumpBridgeTax($entry, $corpId, $division);
                    } else if($entry['ref_type'] == 'player_donation' ||
                             ($entry['ref_type'] == 'corporation_account_withdrawal' && $entry['second_party_id'] == 98287666)) {
                        $other = new PlayerDonation();
                        $other->InsertPlayerDonation($entry, $corpId, $division);
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

}

?>