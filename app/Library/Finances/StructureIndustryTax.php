<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Finances;

use DB;

use App\Library\Esi;

use App\Models\Finances\StructureIndustryTaxJournal;

class StructureIndustryTax {

    public function InsertStructureIndustryTax($journal, $corpId, $division) {

        //Check to see if we can find the entry in the database already.
        //If we don't then add it to the database
        if(!StructureIndustryTaxJournal::where(['id' => $journal['id']])->exists()) {
            $entry = new StructureIndustryTaxJournal;
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
            $entry->date = $esiHelper->DecodeDate($journal['date']);
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