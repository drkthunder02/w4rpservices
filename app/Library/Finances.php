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

    public function CalculateAverageFuelBlock() {

    }

    public function CalculateMonthlyRefineryTaxees($corpId, $month, $overallTax) {
        $currentTime = Carbon::now();
        $monthWanted = $month;
        $untaxed = 0.00;
        //Get the journal entries from the database
        $entries = DB::table('CorpJournals')->where(['corporation_id' => $corpId, 'created_at' => $monthWanted, 'ref_type' => 'reprocessing_tax'])->get();
        foreach($entries as $entry) {
            $untaxed += $entry->tax;
        }
        //The alliance will get 1.0 pts of the tax.  We need to calculate the correct percentage and return the value
        $taxRatio = $overallTax / 1.0;
        $taxed = $untaxed / $taxRatio;

        return $taxed;
    }

    public function CalculateMonthlyMarketTaxes($corpId, $month, $overallTax) {
        //Convert the current time to a time / date
        $currentTime = Carbon::now();
        $monthWanted = $month;
        $untaxed = 0.00;
        //Get the journal entries from the database
        $entries = DB::table('CorpJournals')->where(['corporation_id' => $corpId, 'created_at' => $monthWanted, 'ref_type' => 'brokers_fee'])->get();
        foreach($entries as $entry) {
            $untaxed += $entry->tax;
        }
        //The alliance will get 2.5 pts of the tax.  We need to calculate
        //the correct percentage, and return the value
        $taxRatio = $overallTax / 2.5;
        $taxed = $untaxed / $taxRatio;

        return $taxed;
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
        $check = DB::table('CorpJournals')->where('id', $journal['id'])->get();
        //if we don't find the journal entry, add the journal entry to the database
        if($check->count() === 0) {
            $entry = new \App\Models\CorpJournal;
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
            $entry->date = $journal['date'];
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