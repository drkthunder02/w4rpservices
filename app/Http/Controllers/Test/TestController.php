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

//Models
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
        $lookup = new LookupHelper;
        $ledgers = new Collection;
        $perms = new Collection;
        

        var_dump(auth()->user()->getAlts());
        dd(auth()->user()->altCount());

        //Get all of the users in the database
        $users = User::all();

        //Get a list of the alts for each character, then process the ledgers and combine them to send one mail out
        //in this first part
        foreach($users as $char) {
            $altCount = $char->altCount();
            
            if($altCount > 0) {
                $alts = $char->getAlts();

                foreach($alts as $alt) {
                    $perms->push([
                        'main_id' => $char->character_id,
                        'alt_id' => $alt->character_id,
                        'count' => $altCount,
                    ]);
                } 
            } else {
                $perms->push([
                    'main_id' => $char->character_id,
                    'alt_id' => null,
                    'count' => 0,
                ]);
            }
        }

        return view('test.miningtax.invoice')->with('perms', $perms);
    }

    public function DebugMiningObservers() {
        //Declare variables
        $mailDelay = 15;
        $config = config('esi');
        $mains = array();

        //Get the users from the database
        $mains = User::all();

        //Pluck all the users from the database of ledgers to determine if they are mains or alts.
        $tempMains = Ledger::where([
            'invoiced' => 'Yes',
        ])->where('last_updated', '>', Carbon::now()->subMonths(3))->pluck('character_id');

        dd($tempMains);

        foreach($tempMains as $main) {
            if(!isset($mains[$main->character_id])) {
                $mains[$main->character_id] = $main->character_id;
            }
        }

        dd($mains);

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
                'character_id' => $main->character_id,
                'invoiced' => 'Yes',
            ])->where('last_updated', '>', Carbon::now()->subMonths(3))->count();

            //If there are ledgers for the main, then let's grab them
            if($mainLedgerCount > 0) {
                $mainLedgers = Ledger::where([
                    'character_id' => $main->character_id,
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
            $altCount = $main->altCount();
            //If more than 0 alts, grab all the alts.
            if($altCount > 0) {
                $alts = UserAlt::where([
                    'main_id' => $main->character_id,
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
