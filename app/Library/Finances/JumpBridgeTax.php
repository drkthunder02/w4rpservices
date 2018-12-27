<?php

/** W4RP Services
 * GNU Public License
 */

namespace App\Library\Finances;

use DB;
use Carbon\Carbon;

use App\Library\Esi\Esi;

use App\Models\Finances\JumpBridgeJournal;
use App\Models\User\UserToCorporation;

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
     * Function to get the corporations using the jump bridge over a given time period
     */
    public function CorporationUsage() {
        //Make an array for corporations, and amounts
        $amounts = array();
        $characters = array();
        $data = array();
        $esi = new Esi();

        //Get all of the parties which have utilized the jump bridge
        $parties = DB::table('jump_bridge_journal')
                    ->select('first_party_id')
                    ->groupBy('first_party_id')
                    ->whereTime('date', '>', $this->date)
                    ->get();

        //Run through each party and assign them into a corporation, then add the corporation to the corporation array if they don't 
        //exist in the array.
        foreach($parties as $party) {
            //If the entry in the database lookup table isn't found, add it.
            if(!CharacterToCorporation::where(['character_id' => $party->first_party_id])->exists()) {
                $character = $esi->GetCharacterData($party->first_party_id);
                $corporation = $esi->GetCorporationData($character->corporation_id);
                $char = new CharacterToCorporation;
                $char->character_id = $party->first_party_id;
                $char->character_name = $character->name;
                $char->corporation_id = $character->corporation_id;
                $char->corporation_name = $corporation->name;
                $char->save();
            }

            //Perform the lookup and add the user into the corps array, and the ammount to the amount array
            $char = CharacterToCorporation::where(['character_id' => $party->first_party_id])->get();
            
            //Find the amount utilized from the jump bridge by the character
            $isk = JumpBridgeJournal::where(['first_party_id' => $char->character_id])
                                    ->whereBetween('date', [$this->date, $this->date->addDays(30)])
                                    ->sum('amount');
            
            //We have the character and isk amount, so we need to build an array with these two values as key value pairs.
            $data[$char->corporation_name] = $data[$char->corporation_name] + $isk;
        }

        //Return the data
        return $data;

    }

    /**
     * Returns the overall usage for statistics
     */
    public function OverallTax() {

        //Get the total usage
        $usage = DB::table('jump_bridge_journal')
                    ->select('amount')
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
                    ->sum(['amount']);
        
        return $usage;
    }


}