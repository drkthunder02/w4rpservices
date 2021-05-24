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
        

        //Get all of the users in the database
        $users = User::all();

        //Get a list of the alts for each character, then process the ledgers and combine them to send one mail out
        //in this first part
        foreach($users as $char) {
            $alts = $char->userAlts();

            $mainLedgers = Ledger::where([
                'character_id' => $char->character_id,
                'invoiced' => 'No',
            ])->get();

            if(Ledger::where([
                'character_id' => $char->character_id,
            ])->count() > 0) {
                foreach($mainLedgers as $row) {
                    $ledgers->push([
                        'main_id' => $row->character_id,
                        'character_id' => $row->character_id,
                        'character_name' => $row->character_name,
                        'observer_id' => $row->observer_id,
                        'last_updated' => $row->last_updated,
                        'type_id' => $row->type_id,
                        'ore_name' => $row->ore_name,
                        'quantity' => $row->quantity,
                        'amount' => $row->amount,
                        'invoiced' => $row->invoiced,
                        'invoice_id' => $row->invoice_id,
                    ]);
                }
            }

            foreach($alts as $alt) {
                if($alt->character_id != $char->character_id) {
                    $ledgerRows = Ledger::where([
                        'character_id' => $alt->character_id,
                        'invoiced' => 'No',
                    ])->get();

                    if($ledgerRows->count() > 0) {
                        $ledgers->push([
                            'main_id' => $char->character_id,
                            'character_id' => $alt->character_id,
                            'observer_id' => $row->observer_id,
                            'last_updated' => $row->last_updated,
                            'type_id' => $row->type_id,
                            'ore_name' => $row->ore_name,
                            'quantity' => $row->quantity,
                            'amount' => $row->amount,
                            'invoiced' => $row->invoiced,
                            'invoice_id' => $row->invoice_id,
                        ]);
                    }
                }
            }
        }

        return view('test.miningtax.invoice')->with('ledgers', $ledgers);
    }

    public function DebugMiningObservers() {
        $ledgers = array();
        $lookup = new LookupHelper;
        $config = config('esi');
        $esiHelper = new Esi;

        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
            'corporation_id' => $config['corporation'],
        ]);

        $observers = json_decode($response->raw, true);

        $currentPage = 1;
        $totalPages = 1;

        foreach($observers as $observer) {
            do {
                if($esiHelper->TokenExpired($refreshToken)) {
                    $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
                    $esi = $esiHelper->SetupEsiAuthentication($refreshToken);
                }

                $response = $esi->page($currentPage)
                                ->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}', [
                                    'corporation_id' => $config['corporation'],
                                    'observer_id' => $observer['observer_id'],
                                ]);

                if($currentPage == 1) {
                    $totalPages = $response->pages;
                }

                $tempLedgers = json_decode($response->raw, true);

                foreach($tempLedgers as $ledg) {
                    array_push($ledgers, [
                        'observer_id' => $observer['observer_id'],
                        'character_id' => $ledg['character_id'],
                        'last_updated' => $ledg['last_updated'],
                        'type_id' => $ledg['type_id'],
                        'quantity' => $ledg['quantity'],
                    ]);
                }
            } while($currentPage <= $totalPages);
        }

        return view('test.miningtax.observers')->with('ledgers', $ledgers);
    }
}
