<?php

namespace App\Library\Structures;

use DB;
use Carbon\Carbon;

use App\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Corporation\CorpStructure;
use App\Models\Finances\CorpMarketJournal;
use App\Models\Finances\ReprocessingTaxJournal;

class StructureTaxHelper {
    private $corpId;
    private $refType;
    private $start;
    private $end;

    public function GetTaxes($corpId, $refType, $start, $end) {
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

        //Calculate the total fuel block cost
        $totalFuelCost = $fuelCost * $count;

        //Calculate the total revenue minus the fuel block cost
        $totalRevenue = $revenue - $totalFuelCost;

        //Check to see if the revenue is greater than zero to avoid division by zero error.
        //Then calculate the tax owed which is revenue divided by ratio previously calcualted.
        if($totalRevenue > 0.00) {
            $taxOwed = $totalRevenue / $ratio;
        } else {
            $taxOwed = 0.00;
        }

        //Return the amount
        return $taxOwed;
    }

    public function GetRevenue($corpId, $refType, $start, $end) {
        $revenue = 0.00;
        if($refType == 'Market') { 
            $revenue = MarketTaxJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else if($refType == 'Refinery'){
            $revenue = ReprocessingTaxJournal::where(['ref_type' => 'reprocessing_tax', 'corporation_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else {
            $revenue = 0.00;
        }

        return (float)$revenue;
    }

    private function CalculateTaxRatio($overallTax, $type) {
        //The alliance will get a ratio of the tax.
        //We need to calculate the correct ratio based on structure tax, 
        //Then figure out what is owed to the alliance
        if($type == 'Market') {
            $ratioType = 2.5;
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
        $cost = $fuelBlocks * 20000;

        //Return to the calling function
        return $cost;
    }

    public function GetTimeFrame() {
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

        return (int)$count;
    }
}

?>