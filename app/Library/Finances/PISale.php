<?php

/**
 * W4RP Services
 * GNU Public License
 */

 namespace App\Library\Finances;

 use DB;

 use App\Library\Esi\Esi;

 use App\Models\Finances\PISaleJournal;

 class PISale {
    
    public function InsertPISale($journal, $corpId) {
        //Create the ESI Helper class
        $esiHelper = new Esi;

        //dd($journal);

        //Insert the PI Sale into the database
        if(!PISaleJournal::where(['id' => $journal['journal_ref_id']])->exists()) {
            $entry = new PISaleJournal;
            $entry->id = $journal['journal_ref_id'];
            $entry->corporation_id = $corpId;
            //$entry->division = $division;
            if(isset($journal['client_id'])) {
                $entry->client_id = $journal['client_id'];
            }
            if(isset($journal['date'])) {
                $entry->date = $esiHelper->DecoateDate($journal['date']);
            }
            if(isset($journal['is_buy'])) {
                $entry->is_buy = $journal['is_buy'];
            }
            if(isset($journal['journal_ref_id'])) {
                $entry->journal_ref_id = $journal['journal_ref_id'];
            }
            if(isset($journal['location_id'])) {
                $entry->location_id = $journal['location_id'];
            }
            if(isset($journal['quantity'])) {
                $entry->quantity = $journal['quantity'];
            }
            if(isset($journal['transaction_id'])) {
                $entry->transaction_id = $journal['transaction_id'];
            }
            if(isset($journal['type_id'])) {
                $entry->type_id = $journal['type_id'];
            }
            if(isset($journal['unit_price'])) {
                $entry->unit_price = $journal['unit_price'];
            }
            $entry->save();
        }
    }

 }

?>