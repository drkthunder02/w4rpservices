<?php

/** W4RP Services
 * GNU Public License
 */

namespace App\Library\Finances;

//Internal Library
use DB;
use Carbon\Carbon;

//Library
use App\Library\Esi\Esi;

//Models
use App\Models\Finances\JumpBridgeJournal;

class JumpBridgeTax {
    private $date;
    private $days;

    public function __construct($days = null) {
        if($days === null) {
            $this->date = Carbon::now();
            $this->days = 0;
        } else {
            $this->date = Carbon::now()->subDays($days);
            $this->days = $days;
        }
    }

    /**
     * Function to insert journal entries into the database
     */
    public function InsertJumpBridgeTax($journal, $corpId, $division) {
        //Create the ESI Helper class
        $esiHelper = new Esi;

        //Check to see if we can find the entry in the database already.
        //If we don't then add it to the database
        if(!JumpBridgeJournal::where(['id' => $journal['id']])->exists()) {
            $entry = new JumpBridgeJournal;
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

    /**
     * Returns the overall usage for statistics
     */
    public function OverallTax() {

        //Get the total usage
        $usage = JumpBridgeJournal::select('amount')
                                    ->whereTime('date', '>', $this->date)
                                    ->sum('amount');
        
        //Return the usage
        return $usage;
    }

    /**
     * Returns a specific briddge usage statistics for overall usage
     */
    public function JBOverallUsage($structure) {
        
        $usage = DB::table('jump_bridge_journal')
                    ->select('amount')
                    ->where('context_id', $structure)
                    ->sum('amount');
        
        return $usage;
    }


}