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

    public function displayTaxHistory() {
        //Make the helper esi class
        $helper = new Esi();

        //Get the character's corporation from esi
        $corpId = $helper->FindCorporationId(Auth::user()->character_id);

        //Get the dates we are working with
        $dates = $this->GetLongTimeFrame();
        //Create the totalTaxes array
        $totalTaxes = array();

        //Create the array for totalTaxes in order to send in the data to the view
        for($i = 0; $i < 12; $i++) {
            $totalTaxes[$i] = [
                'MarketTax' => number_format($this->GetTaxes($corpId, 'Market', $dates[$i]['start'], $dates[$i]['end']), 2, '.', ','),
                'MarketRevenue' => number_format($this->GetRevenue($corpId, 'Market', $dates[$i]['start'], $dates[$i]['end']), 2, '.', ','),
                'RefineryTax' => number_format($this->GetTaxes($corpId, 'Refinery', $dates[$i]['start'], $dates[$i]['end']), 2, '.', ','),
                'RefineryRevenue' => number_format($this->GetRevenue($corpId, 'Refinery', $dates[$i]['start'], $dates[$i]['end']), 2, '.', ','),
                'start' => $dates[$i]['start']->toFormattedDateString(),
            ];
        }

        return view('structures.taxhistory')->with('totalTaxes', $totalTaxes);
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
            'thisMonthMarket' => number_format($this->GetTaxes($corpId, 'Market', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'thisMonthRefinery' => number_format($this->GetTaxes($corpId, 'Refinery', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'lastMonthMarket' => number_format($this->GetTaxes($corpId, 'Market', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'lastMonthRefinery' => number_format($this->GetTaxes($corpId, 'Refinery', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'thisMonthRevMarket' => number_format($this->GetRevenue($corpId, 'Market', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'thisMonthRevRefinery' => number_format($this->GetRevenue($corpId, 'Refinery', $dates['ThisMonthStart'], $dates['ThisMonthEnd']), 2, '.', ','),
            'lastMonthRevMarket' => number_format($this->GetRevenue($corpId, 'Market', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'lastMonthRevRefinery' => number_format($this->GetRevenue($corpId, 'Refinery', $dates['LastMonthStart'], $dates['LastMonthEnd']), 2, '.', ','),
            'thisMonthStart' => $dates['ThisMonthStart']->toFormattedDateString(),
            'lastMonthStart' => $dates['LastMonthStart']->toFormattedDateString(),
        ];

        return view('structures.taxes')->with('totalTaxes', $totalTaxes);
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
        
        $revenue = $revenue - ($fuelCost * $count);
        
        //Calculate the tax owed which is revenue divided by ratio previously calculated
        $taxOwed = $revenue / $ratio;
        //Check for negative number, and if negative, zero it out.
        if($taxOwed < 0.00){
            $taxOwed = 0.00;
        }
        //Return the amount
        return $taxOwed;
    }

    private function GetRevenue($corpId, $refType, $start, $end) {
        $revenue = 0.00;
        if($refType == 'Market') {
            $revenue = CorpJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else if($refType == 'Refinery'){
            $revenue = CorpJournal::where(['ref_type' => 'reprocessing_tax', 'corporation_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else {
            $revenue = 0.00;
        }

    
        return $revenue;
    }

    private function CalculateTaxRatio($overallTax, $type) {
        //The alliance will get a ratio of the tax.
        //We need to calculate the correct ratio based on structure tax, 
        //Then figure out what is owed to the alliance
        if($type == 'Market') {
            $ratioType = 2.0;
        } else if($type == 'Refinery') {
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

    private function GetLongTimeFrame() {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        $dates = array();
        $dates[0] = [
            'start' => $start,
            'end' => $end,
        ];

        //Cycle through the for loop and build the array
        for($i = 1; $i < 12; $i++) {
            $start->subMonth();
            $end->subMonth();
            $dates[$i] = [
                'start' => $start,
                'end' => $end,
            ];
        }

        return $dates;
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
        $tax = CorpStructure::where(['corporation_id' => $corpId, 'structure_type' => $structureType])->avg('tax');

        return (float) $tax;
    }

    private function GetStructureCount($corpId, $structureType) {
        $count = CorpStructure::where(['corporation_id' => $corpId, 'structure_type' => $structureType])->count();

        return (float) $count;
    }
}
