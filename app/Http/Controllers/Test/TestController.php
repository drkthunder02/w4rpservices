<?php

namespace App\Http\Controllers\Test;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

//Application Library
use App\Library\Helpers\LookupHelper;
use App\Library\Esi\Esi;
use App\Library\Moons\MoonCalc;

//Models
use App\Models\MoonRental\AllianceMoon;
use App\Models\MoonRental\AllianceMoonOre;
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Ledger;
use App\Models\MiningTax\Observer;
use App\Models\User\UserAlt;
use App\Models\User\User;

class TestController extends Controller
{
    public function displayCharTest() {
        $lookup = new LookupHelper;

        $config = config('esi');

        $char = $lookup->GetCharacterInfo($config['primary']);

        return view('test.char.display')->with('char', $char);
    }

    public function DebugMiningTaxesInvoices() {
        //Declare variables
        $mailDelay = 15;
        $mains = new Collection;
        $perms = null;
        $config = config('esi');

        /**
         * This section will determine if users are mains or alts of a main.
         * If they are mains, we keep the key.  If they are alts of a main, then we delete
         * the key from the collection.
         */

        //Pluck all the users from the database of ledgers to determine if they are mains or alts.
        $tempMains = Ledger::where([
            'invoiced' => 'No',
        ])->where('last_updated', '>', Carbon::now()->subDays(7))->pluck('character_id');
        
        //Get the unique character ids from the ledgers in the previous statement
        $tempMains = $tempMains->unique()->values()->all();

        //Cycle through the array of mains, and remove any characters which are in the User Alt table,
        //as those characters will be grouped with their correct main later.
        for($i = 0; $i < sizeof($tempMains); $i++) {
            if(UserAlt::where(['character_id' => $tempMains[$i]])->count() == 0) {
                $mains->push($tempMains[$i]);
            }
        }

        /**
         * For each of the users, let's determine if there are any ledgers,
         * then determine if there are any alts and ledgers associated with the alts.
         */
        foreach($mains as $main) {
            //Declare some variables for each run through the for loop
            $ledgers = new Collection;

            //Count the ledgers for the main
            $mainLedgerCount = Ledger::where([
                'character_id' => $main,
                'invoiced' => 'No',
            ])->where('last_updated', '>', Carbon::now()->subDays(7))->count();

            //If there are ledgers for the main, then let's grab them
            if($mainLedgerCount > 0) {
                $mainLedgers = Ledger::where([
                    'character_id' => $main,
                    'invoiced' => 'No',
                ])->where('last_updated', '>', Carbon::now()->subDays(7))->get();

                //Cycle through the entries, and add them to the ledger to send with the invoice
                foreach($mainLedgers as $row) {
                    $ledgers->push([
                        'character_id' => $row->character_id,
                        'character_name' => $row->character_name,
                        'observer_id' => $row->observer_id,
                        'type_id' => $row->type_id,
                        'ore_name' => $row->ore_name,
                        'quantity' => (int)$row->quantity,
                        'amount' => (float)$row->amount,
                        'last_updated' => $row->last_updated,
                    ]);
                }
            }

            //Get the alt count for the main character
            $altCount = UserAlt::where(['main_id' => $main])->count();
            //If more than 0 alts, grab all the alts.
            if($altCount > 0) {
                $alts = UserAlt::where([
                    'main_id' => $main,
                ])->get();

                //Cycle through the alts, and get the ledgers, and push onto the stack
                foreach($alts as $alt) {
                    $altLedgerCount = Ledger::where([
                        'character_id' => $alt->character_id,
                        'invoiced' => 'No',
                    ])->where('last_updated', '>', Carbon::now()->subDays(7))->count();

                    if($altLedgerCount > 0) {
                        $altLedgers = Ledger::where([
                            'character_id' => $alt->character_id,
                            'invoiced' => 'No',
                        ])->where('last_updated', '>', Carbon::now()->subDays(7))->get();

                        foreach($altLedgers as $row) {
                            $ledgers->push([
                                'character_id' => $row->character_id,
                                'character_name' => $row->character_name,
                                'observer_id' => $row->observer_id,
                                'type_id' => $row->type_id,
                                'ore_name' => $row->ore_name,
                                'quantity' => (int)$row->quantity,
                                'amount' => (float)$row->amount,
                                'last_updated' => $row->last_updated,
                            ]);
                        }
                    }
                }
            }

            /**
             * Send the collected information over to the function to send the actual mail
             */
            if($ledgers->count() > 0) {
                $invoiceAmount = round(((float)$ledgers->sum('amount') * (float)$config['mining_tax']), 2);
                var_dump($ledgers);
                var_dump($invoiceAmount);
                var_dump(round(((float)$ledgers->sum('amount') * (float)$config['mining_tax']), 2));
            }

        }

        return view('test.miningtax.invoice')->with('perms', $perms);
    }

    public function DebugMiningObservers() {
        //Declare variables
        $mailDelay = 15;
        $config = config('esi');
        $mains = new Collection;

        /**
         * This section will determine if users are mains or alts of a main.
         * If they are mains, we keep the key.  If they are alts of a main, then we delete
         * the key from the collection.
         */

        //Pluck all the users from the database of ledgers to determine if they are mains or alts.
        $tempMains = Ledger::where([
            'invoiced' => 'Yes',
        ])->where('last_updated', '>', Carbon::now()->subMonths(3))->pluck('character_id');
        
        //Get the unique character ids from the ledgers in the previous statement
        $tempMains = $tempMains->unique()->values()->all();

        for($i = 0; $i < sizeof($tempMains); $i++) {
            if(UserAlt::where(['character_id' => $tempMains[$i]])->count() == 0) {
                $mains->push($tempMains[$i]);
            }
        }

        /**
         * For each of the users, let's determine if there are any ledgers,
         * then determine if there are any alts and ledgers associated with the alts.
         */
        foreach($mains as $main) {
            //Declare some variables for each run through the for loop
            $mainLedgerCount = 0;
            $ledgers = new Collection;

            //Count the ledgers for the main
            $mainLedgerCount = Ledger::where([
                'character_id' => $main,
                'invoiced' => 'Yes',
            ])->where('last_updated', '>', Carbon::now()->subMonths(3))->count();

            //If there are ledgers for the main, then let's grab them
            if($mainLedgerCount > 0) {
                $mainLedgers = Ledger::where([
                    'character_id' => $main,
                    'invoiced' => 'Yes',
                ])->where('last_updated', '>', Carbon::now()->subMonths(3))->get();

                //Cycle through the entries, and add them to the ledger to send with the invoice
                foreach($mainLedgers as $row) {
                    $ledgers->push([
                        'character_id' => $row->character_id,
                        'character_name' => $row->character_name,
                        'observer_id' => $row->observer_id,
                        'type_id' => $row->type_id,
                        'ore_name' => $row->ore_name,
                        'quantity' => $row->quantity,
                        'amount' => (float)$row->amount,
                        'last_updated' => $row->last_updated,
                    ]);
                }
            }

            //Get the alt count for the main character
            $altCount = UserAlt::where(['main_id' => $main])->count();
            //If more than 0 alts, grab all the alts.
            if($altCount > 0) {
                $alts = UserAlt::where([
                    'main_id' => $main,
                ])->get();

                //Cycle through the alts, and get the ledgers, and push onto the stack
                foreach($alts as $alt) {
                    $altLedgerCount = Ledger::where([
                        'character_id' => $alt->character_id,
                        'invoiced' => 'Yes',
                    ])->where('last_updated', '>', Carbon::now()->subMonths(3))->count();

                    if($altLedgerCount > 0) {
                        $altLedgers = Ledger::where([
                            'character_id' => $alt->character_id,
                            'invoiced' => 'Yes',
                        ])->where('last_updated', '>', Carbon::now()->subMonths(3))->get();

                        foreach($altLedgers as $row) {
                            $ledgers->push([
                                'character_id' => $row->character_id,
                                'character_name' => $row->character_name,
                                'observer_id' => $row->observer_id,
                                'type_id' => $row->type_id,
                                'ore_name' => $row->ore_name,
                                'quantity' => $row->quantity,
                                'amount' => (float)$row->amount,
                                'last_updated' => $row->last_updated,
                            ]);
                        }
                    }
                }
            }
        
            if($ledgers->count() > 0) {
                var_dump($ledgers);
                var_dump(round(((float)$ledgers->sum('amount') * (float)$config['mining_tax']), 2));
            }
        }
    }
}
