<?php

namespace App\Library\Structures;

use DB;
use Carbon\Carbon;

use App\User;

use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Corporation\CorpStructure;
use App\Models\Corporation\CorpTaxRatio;
use App\Models\Finances\CorpMarketJournal;
use App\Models\Finances\ReprocessingTaxJournal;
use App\Models\Finances\StructureIndustryTaxJournal;

class StructureTaxHelper {
    private $corpId;
    private $refType;
    private $start;
    private $end;

    public function __construct($corp = null, $ref = null, $st = null, $en = null) {
        $this->corpId = $corp;
        $this->refType = $ref;
        $this->start = $st;
        $this->end = $en;
    }

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
        //$ratio = $this->CalculateTaxRatio($corpId, $tax, $refType);
        //Get the ratio from the table
        $ratio = CorpTaxRatio::where([
            'corporation_id' => $corpId,
            'structure_type' => $refType,
        ])->get(['ratio']);
        $ratio = $ratio[0];
        if($ratio == null) {
            $ratio = 1.0;
        }
        
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

    public function GetIndustryRevenue($start, $end) {
        $revenue = 0.00;

        $revenue = StructureIndustryTaxJournal::where(['ref_type' => 'facility_industry_tax', 'second_party_id'  => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        return $revenue;
    }

    public function GetRevenue($corpId, $refType, $start, $end) {
        $revenue = 0.00;
        if($refType == 'Market') {
            //Get the revenue from the corp_market_journals table and add it up.
            $revenue = CorpMarketJournal::where(['ref_type' => 'brokers_fee', 'corporation_id' => $corpId, 'second_party_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else if($refType == 'Refinery'){
            //Get the revenue from the reprocessing_tax_journal table and add it up.
            $revenue = ReprocessingTaxJournal::where(['ref_type' => 'reprocessing_tax', 'corporation_id' => $corpId, 'second_party_id' => $corpId])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        } else {
            //If it's not from one of the above tables, then it doesn't mean anything, so return nothing.
            $revenue = 0.00;
        }

        return (float)$revenue;
    }

    private function CalculateTaxRatio($corpId, $overallTax, $type) {
        //Get the ratio based on what was decided upon for the ratio of taxes.
        //Default rate is 2.5 ratio.
        

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

    /**
     * Returns a set of dates from now until the amount of months has passed
     * 
     * @var integer
     * @returns array
     */
    public function GetTimeFrameInMonths($months) {
        //Declare an array of dates
        $dates = array();
        //Setup the start of the array as the basis of our start and end dates
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        if($months == 1) {
            $dates = [
                'start' => $start,
                'end' => $end,
            ];

            return $dates;
        }

        //Create an array of dates
        for($i = 0; $i < $months; $i++) {
            if($i == 0) {
                $dates[$i]['start'] = $start;
                $dates[$i]['end'] = $end;
            }
            
            $start = Carbon::now()->startOfMonth()->subMonths($i);
            $end = Carbon::now()->endOfMonth()->subMonths($i);
            $end->hour = 23;
            $end->minute = 59;
            $end->second = 59;
            $dates[$i]['start'] = $start;
            $dates[$i]['end'] = $end;
        }

        //Return the dates back to the calling function
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