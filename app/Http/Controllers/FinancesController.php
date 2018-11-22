<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Socialite;
use Auth;

use App\User;
use App\Library\Finances;
use App\Library\Esi;
use App\Library\SeatHelper;
use App\Models\CorpJournal;

use Carbon\Carbon;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class FinancesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Director');
    }

    public function displayWallet() {
        $helper = new Finances();

        $helper->GetWalletJournal(1, 92626011);
        dd($helper);
    }

    public function displayTaxes() {
        //Make the helper esi class
        $helper = new Esi();
        //Set the carbon date for the first day of the month
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $totalTax = 0.00;
        //Get the character's corporation from esi
        $corporation = $helper->FindCorporationName(Auth::user()->character_id);
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);
        //Get the taxes for the corporation
        $taxes = DB::table('CorpJournals')
            ->where(['corporation_id'=> $corpId, 'ref_type' => 'brokers_fee'])
            ->whereBetween($start, $end)
            ->get();
        foreach($taxes as $tax) {
            $totalTax += $tax->amount;
        }

        return view('finances.taxes')->with('totalTax', $totalTax);

    }
}
