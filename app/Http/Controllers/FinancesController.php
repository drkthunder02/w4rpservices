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
        //Make the helper class for finances
        $hFinances = new Finances();
        //Set the carbon date for the first day of this month, last day of this month, and the previous month's first and last days.
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;
        $startLast = Carbon::now()->firstDayOfPreviousMonth();
        $endLast = Carbon::now()->lastDayOfPreviousMonth();
        $endLast->hour = 23;
        $endLast->minute = 59;
        $endLast->second = 59;

        //Get the character's corporation from esi
        $corporation = $helper->FindCorporationName(Auth::user()->character_id);
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);

        //Get the number of structures registered to a corporation
        $citadelCount = DB::select("SELECT COUNT(structure_name) FROM CorpStructures WHERE corporation_id='" . $corporation . "' AND structure_type='Citadel'");
        $refineryCount = DB::select("SELECT COUNT(structure_name) FROM CorpStructures WHERE corporation_id='" . $corporation . "' aND structure_type='Refinery'");

        //Get the taxes for the corporation
        //SELECT SUM(amount) FROM CorpJournals WHERE ref_type='brokers_fee' AND date BETWEEN '2018-11-01 00:00:00' AND '2018-11-31 23:59:59'
        $monthTaxesMarket = DB::select("SELECT SUM(amount) FROM CorpJournals WHERE ref_type='brokers_fee' AND corporation_id='" . $corpId . "' AND date BETWEEN '" . $start . "' AND '" . $end . "'");
        $lastTaxesMarket = DB::select("SELECT SUM(amount) FROM CorpJournals WHERE ref_type='brokers_fee' AND corporation_id='" . $corpId . "' AND date BETWEEN '" . $startLast . "' AND '" . $endLast . "'");
        $monthTaxesReprocessing = DB::select("SELECT SUM(amount) FROM CorpJournals WHERE ref_type='reprocessing_fee' AND corporation_id='" . $corpId . "' AND date BETWEEN '" . $start . "' AND '" . $end . "'");
        $lastTaxesReprocessing = DB::select("SELECT SUM(amount) FROM CorpJournals WHERE ref_type='reprocessing_fee' AND corporation_id='" . $corpId . "' AND date BETWEEN '" . $startLast . "' AND '" . $endLast . "'");

        /**
         * In this next section we are removing the cost of fuel blocks from one structure
         */
        $monthTaxesMarket = $monthTaxesMarket - ($hFinaces->CalculateFuelBlockCost('market') * $citadelCount);
        if($monthTaxesMarket < 0.00) {
            $monthTaxesMarket = 0.00;
        }

        $lastTaxesMarket = $lastTaxesMarket - ($hFinances->CalculateFuelBlocksCost('market') * $citadelCount);
        if($lastTaxesMarket < 0.00) {
            $lastTaxesMarket = 0.00;
        }

        $monthTaxesReprocessing = $monthTaxesReprocessing - ($hFinaces->CalculateFuelBlockCost('reprocessing') * $refineryCount);
        if($monthTaxesReprocessing < 0.00) {
            $monthTaxesReprocessing = 0.00;
        }

        $lastTaxesReprocessing = $lastTaxesReprocessing - ($hFinances->CalculateFuelBlockCost('reprocessing') * $refineryCount);
        if($lastTaxesReprocessing < 0.00) {
            $lastTaxesReprocessing = 0.00;
        }

        //Create the array to pass to the blade view
        $totalTaxes = [
            'thisMonthReprocessing' => $monthTaxesReprocessing,
            'lastMonthReprocessing' => $lastTaxesReprocessing,
            'thisMonthMarket' => $monthTaxesMarket,
            'lastMonthMarket' => $lastTaxesMarket,
        ];

        return view('finances.taxes')->with('totalTaxes', $totalTaxes);

    }
}
