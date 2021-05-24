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
        $ledgers = array();
        $lookup = new LookupHelper;
        $config = config('esi');
        $esiHelper = new Esi;

        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 600);

        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        $observers = [1035697195484, 1035697216662];

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

        ini_set('memory_limit', $memory_limit);
        ini_set('max_execution_time', $time_limit);

        return view('test.miningtax.observers')->with('ledgers', $ledgers)
                                               ->with('observers', $observers);
    }
}
