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

        //Get the character's corporation from esi
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);

        //Get the dates we are working with
        $dates = $this->GetTimeFrame();

        //Get the market taxes for this month from the database
        $totalTaxes = [
            'thisMonthMarket' => $this->GetTaxes($corpId, 'Market', $dates['ThisMonthStart'], $dates['ThisMonthEnd']),
            'thisMonthRefinery' => $this->GetTaxes($corpId, 'Refinery', $dates['ThisMonthStart'], $dates['ThisMonthEnd']),
            'lastMonthMarket' => $this->GetTaxes($corpId, 'Market', $dates['LastMonthStart'], $dates['LastMonthEnd']),
            'lastMonthRefinery' => $this->GetTaxes($corpId, 'Refinery', $dates['LastMonthStart'], $dates['LastMonthEnd']),
            'thisMonthRevMarket' => $this->GetRevenue($corpId, 'Market', $dates['ThisMonthStart'], $dates['ThisMonthEnd']),
            'thisMonthRevRefinery' => $this->GetRevenue($corpId, 'Refinery', $dates['ThisMonthStart'], $dates['ThisMonthEnd']),
            'lastMonthRevMarket' => $this->GetRevenue($corpId, 'Market', $dates['LastMonthStart'], $dates['LastMonthEnd']),
            'lastMonthRevRefinery' => $this->GetRevenue($corpId, 'Refinery', $dates['LastMonthStart'], $dates['LastMonthEnd']),
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

    private function GetTaxes($corpId, $refType, $start, $end) {
        $taxOwed = 0.00;
        //Get the number of structures of a certain type
        $count = $this->GetStructureCount($corpId, $refType);

        //Calculate the fuel cost for one type of structure
        $fuelCost = $this->CalculateFuelBlockCost($refType);

        //Calculate the average tax for a given structure type
        $tax = $this->GetStructureTax($corpId, $refType);

        //Calculate the tax ratio to later be divided against the tax to find the
        //actual tax owed to the alliance.  Revenue will be a separate function
        $ratio = $this->CalculateTaxRatio($tax, $refType, $start, $end);

        //Get the total taxes produced by the structure(s) over a given set of dates
        $revenue = $this->GetRevenue($corpId, $refType, $start, $end);
        //Calculate the tax owed which is revenue divided by ratio previously calculated
        $taxOwed = $revenue / $ratio;
        //Check for negative number, and if negative, zero it out.
        if($taxOwed < 0.00){
            $taxOwed = 0.00;
        }
        //Return the amount
        $taxOwed = number_format($taxOwed,'2', '.', ',');
        return $taxOwed;
    }

    private function GetRevenue($corpId, $refType, $start, $end) {
        $revenue = 0.00;
        if($refType === 'Market') {
            $revenue = CorpJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else if($refType === 'Refinery'){
            $revenue = CorpJournal::where(['ref_type' => 'reprocessing_tax', 'corporation_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else {
            $revenue = 0.00;
        }

        $revenue = number_format($revenue, 2, '.', ',');
        return $revenue;
    }

    private function CalculateTaxRatio($overallTax, $type) {
        //The alliance will get a ratio of the tax.
        //We need to calculate the correct ratio based on structure tax, 
        //Then figure out what is owed to the alliance
        if($type === 'Market') {
            $ratioType = 2.0;
        } else if($type === 'Refinery') {
            $ratioType = 1.0;
        } else {
            $ratioType = 1.0;
        }
        //Calculate the ratio since we have the base percentage the alliance takes
        $taxRatio = $overallTax / $ratioType;

        //Return what is owed to the alliance
        return $taxRatio;
    }

    private function CalculateFuelBlockCost($type) {
        //Calculate how many fuel blocks are used in a month by a structure type
        if($type === 'Market') {
            $fuelBlocks = 24*30*32;
        } else if ($type === 'Refinery') {
            $fuelBlocks = 24*30*8;
        } else {
            $fuelBlocks = 0;
        }

        //Multiply the amount of fuel blocks used by the structure by 20,000.
        $cost = $fuelBlocks * 20000.00;
        //Return to the calling function
        return $cost;
    }

    private function GetTimeFrame() {
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

        $dates = [
            'ThisMonthStart' => $start,
            'ThisMonthEnd' => $end,
            'LastMonthStart' => $startLast,
            'LastMonthEnd' => $endLast,
        ];

        return $dates;
    }

    private function GetStructureTax($corpId, $structureType) {
        return CorpStructure::where(['corporation_id' => $corpId, 'structure_type' => $structureType])->avg('tax');
    }

    private function GetStructureCount($corpId, $structureType) {
        return CorpStructure::where(['corporation_id' => $corpId, 'structure_type' => $structureType])->count();
    }
}
