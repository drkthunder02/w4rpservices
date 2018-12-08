<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Corporation\CorpJournal;
use App\Models\Corporation\CorpStructure;

use App\Library\FinanceHelper;
use App\Library\Esi;

class StructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:structure.operator');
    }

    public function displayTaxes() {
        //Make the helper esi class
        $helper = new Esi();
        //Make the helper class for finances
        $hFinances = new FinanceHelper();
        //Set the carbon date for the first day of this month, last day of this month, and the previous month's first and last days.
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;
        $startLast = new Carbon('first day of last month');
        $endLast = new Carbon('last day of last month');
        $endLast->hour = 23;
        $endLast->minute = 59;
        $endLast->second = 59;

        //Get the character's corporation from esi
        //$corporation = $helper->FindCorporationName(Auth::user()->character_id);
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);

        //Get the number of structures registered to a corporation
        $citadelCount = CorpStructure::where(['corporation_id' => $corpId, 'structure_type' => 'Citadel'])->count();
        dd($citadelCount);
        $tempMonthTaxesMarket = CorpJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corpId])
                                        ->whereBetween('date', [$start, $end])
                                        ->sum('amount');
        $tempLastTaxesMarket = CorpJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corpId])
                                        ->whereBetween('date', [$startLast, $endLast])
                                        ->sum('amount');

        /**
         * In this next section we are removing the cost of fuel blocks from one structure
         */
        $marketFuelCost = $hFinances->CalculateFuelBlockCost('market');

        /**
         * Calculate the final taxes and send to display
         */
        $mTax = CorpStructure::where(['corporation_id' => $corpId, 'structure_type' => 'Citadel'])->avg('tax');

        $monthTaxesMarket = $tempMonthTaxesMarket - ($marketFuelCost * $citadelCount);
        $monthTaxesMarket = $hFinances->CalculateTax($monthTaxesMarket, $mTax, 'market');
        if($monthTaxesMarket < 0.00) {
            $monthTaxesMarket = 0.00;
        }

        $lastTaxesMarket = $tempLastTaxesMarket - $marketFuelCost;
        $lastTaxesMarket = $hFinances->CalculateTax($lastTaxesMarket, $mTax, 'market');
        if($lastTaxesMarket < 0.00) {
            $lastTaxesMarket = 0.00;
        }

        //Create the array to pass to the blade view
        $totalTaxes = [
            'thisMonthMarket' => number_format($monthTaxesMarket, 2, '.', ','),
            'lastMonthMarket' => number_format($lastTaxesMarket, 2, '.', ','),
            'thisMoMarketGeneration' => number_format($tempMonthTaxesMarket, 2, '.', ','),
            'lastMoMarketGeneration' => number_format($tempLastTaxesMarket, 2, '.', ','),
        ];

        return view('structures.taxes')->with('totalTaxes', $totalTaxes);
    }

    public function displayTaxesHistory() {
        //Make the helper ESI Class
        $helper = new Esi();
        //Make the helper class for finances
        $hFinances = new FinanceHelper();

        //Get the character's corporation from esi
        $corporation = $helper->FindCorporationName(Auth::user()->character_id);
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);

        //Set the carbon date for the first day of this month, last day of this month
        $currentStart = Carbon::now()->startOfMonth();
        $currentEnd = Carbon::now()->endOfMonth();
        //Setup the currentEnd to end at 23:59:59
        $currentEnd->hour = 23;
        $currentEnd->minute = 59;
        $currentEnd->second = 59;

        $dates = $this->RenderDates();
        $totalTaxes = array();
        $i = 0;
        foreach($dates as $date) {
            //Get the taxes for each month and store in the totalTaxes array and store the date as well.
            $start = $date;
            $end = new Carbon($date);
            $end = $end->endOfMonth();            
            $end->hour = 23;
            $end->minute = 59;
            $end->second = 59;

            //Get the number of structures registered to a corporation
            $citadelCount = DB::select("SELECT COUNT(structure_name) FROM CorpStructures WHERE corporation_id='" . $corporation . "' AND structure_type='Citadel'");

            //Get the taxes for each type from the database
            $marketTaxes = DB::select("SELECT SUM(amount) FROM CorpJournals WHERE ref_type='brokers_fee' AND corporation_id='" . $corpId . "' AND date BETWEEN '" . $start . "' AND '" . $end . "'");
           
            /**
             * In this next section we are going to remove the cost of fuel blocks from the structure taxes
             */
            //Market Taxes with fuel blocks added in
            $marketTaxes = $marketTaxes - ($hFinances->CalculateFuelBlockCost('market') * $citadelCount);
            if($marketTaxes < 0.00) {
                $marketTaxes = 0.00;
            }

            //Add to the totalTaxes array to be sent out with the view
            $totalTaxes[$i] = [
                'date' => $start,
                'market' => number_format($marketTaxes, 2, '.', ','),
            ];
            //Increment $i for the next iteration
            $i++;
        }

        //Return the data to the view
        return view('structures.taxhistory')->with('totalTaxes', $totalTaxes);
    }

    private function RenderDates()
    {
        $start = Carbon::now()->subYear()->startOfYear();
        $months_to_render = Carbon::now()->diffInMonths($start);

        $dates = [];

        for($i = 0; $i <= $months_to_render; $i++) {
            $dates[] = $start->toDateTimeString();
            $start->addMonth();
        }

        return $dates;
    }
}
