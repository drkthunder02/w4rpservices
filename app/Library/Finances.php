<?php

/**
 * W4RP Services
 * GNU Public License
 */

 namespace App\Library;

use DB;

use App\Models\EsiScope;
use App\Models\EsiToken;
use App\Models\CorpJournal;

use App\Library\Esi;

use Carbon\Carbon;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class Finances {

    public function CalculateFuelBlockCost($type) {
        //Calculate how many fuel blocks are used in a month by a structure type
        if($type === 'market') {
            $fuelBlocks = 30*32*24;
        } else if ($type === 'reprocessing') {
            $fuelBlocks = 8*30*24;
        } else {
            $fuelBlocks = 0;
        }

        //Multiply the amount of fuel blocks used by the structure by 20,000.
        $cost = $fuelBlocks * 20000;
        //Return to the calling function
        return $cost;
    }

    public function CalculateTax($taxAmount, $overallTax, $type) {
        //The alliance will get a ratio of the tax.
        //We need to calculate the correct ratio based on structure tax, 
        //Then figure out what is owed to the alliance
        if($type === 'market') {
            $ratioType = 2.5;
        } else if ($type === 'refinery') {
            $ratioType = 1.0;
        } else {
            $ratioType = 1.5;
        }
        //Calculate the ratio since we have the base percentage the alliance takes
        $taxRatio = $overallTax / $ratioType;
        //Calculate the tax owed to the alliance by taking the tax amount collected
        //and divide by the tax ratio.
        $amount = $taxAmount / $taxRatio;

        //Return what is owed to the alliance
        return $amount;
    }

    public function GetWalletJournal($division, $charId) {
        //Get the ESI token for the corporation to add new wallet journals into the database
        $token = DB::table('EsiTokens')->where('character_id', $charId)->get();
        //Disable all caching by setting the NullCache as the preferred cache handler.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;
        $configuration->logfile_location = '/var/www/w4rpservices/storage/logs/eseye';
        //Create the ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        //Create the esi class variable
        $esi = new Eseye($authentication);
        //Try an esi call to get public data to get the character information for corp id.
        try {
            $character = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
            return $e->getEsiResponse();
        }

        //Try the ESI call to get the wallet journal
        try {
            $journals = $esi->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                'corporation_id' => $character->corporation_id,
                'division'  => $division,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
            return $e->getEsiResponse();
        }
        //Decode the journal from json into an array for future processing
        $journals = json_decode($journals->raw, true);
        //For each journal array, attempt to store in the database
        foreach($journals as $entry) {
            if($entry['ref_type'] == 'brokers_fee' || $entry['ref_type'] == 'reprocessing_tax') {
                $this->PutWalletJournal($entry, $character->corporation_id, $division);
            }
        }
    }

    private function PutWalletJournal($journal, $corpId, $division) {
        //Create ESI Helper class
        $esiHelper = new Esi;
        $date = $esiHelper->DecodeDate($journal['date']);

        $check = DB::table('CorpJournals')->where('id', $journal['id'])->get();
        //if we don't find the journal entry, add the journal entry to the database
        if($check->count() === 0) {
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

?>