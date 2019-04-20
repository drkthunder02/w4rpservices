<?php

namespace App\Library\Taxes;

use DB;
use Carbon\Carbon;

use App\User;

use App\Models\User\UserRole;
use App\Models\User\UserPermission;

use App\Models\Finances\ReprocessingTaxJournal;
use App\Models\Finances\StructureIndustryTaxJournal;
use App\Models\Finances\PlanetProductionTaxJournal;
use App\Models\Finances\OfficeFeesJournal;
use App\Models\Finances\CorpMarketJournal;
use App\Models\Finances\JumpBridgeJournal;
use App\Models\Finances\PISaleJournal;

class TaxesHelper {

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

    public function GetJumpGateGross($start, $end) {
        $revenue = 0.00;

        $revenue = JumpBridgeJournal::where(['ref_type' => 'structure_gate_jump', 'second_party_id' => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        return $revenue;
    }

    public function GetMarketGross($start, $end) {
        $revenue = 0.00;

        $revenue = CorpMarketJournal::where(['ref_type' => 'brokers_fee', 'second_party_id' => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        return $revenue;
    }

    public function GetIndustryGross($start, $end) {
        $revenue = 0.00;

        $revenue = StructureIndustryTaxJournal::where(['ref_type' => 'industry_job_tax', 'second_party_id'  => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        return $revenue;
    }

    public function GetReprocessingGross($start, $end) {
        $revenue = 0.00;

        $revenue = ReprocessingTaxJournal::where(['ref_type' => 'reprocessing_tax', 'second_party_id' => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        return $revenue;
    }

    public function GetPIGross($start, $end) {
        $revenueImport = 0.00;
        $revenueExport = 0.00;

        $revenueImport = PlanetProductionTaxJournal::where(['ref_type' => 'planetary_import_tax', 'second_party_id' => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');
        $revenueExport = PlanetProductionTaxJournal::where(['ref_type' => 'planetary_export_tax', 'second_party_id' => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        $finalRevenue = $revenueImport + $revenueExport;

        return $finalRevenue;
    }

    public function GetOfficeGross($start, $end) {
        $revenue = 0.00;

        $revenue = OfficeFeesJournal::where(['ref_type' => 'office_rental_fee', 'second_party_id' => '98287666'])
                                ->whereBetween('date', [$start, $end])
                                ->sum('amount');

        return $revenue;
    }

    public function GetPiSalesGross($start, $end) {
        $revenue = 0.00;

        $grosses = PISaleJournal::whereBetween('date', [$start, $end]);

        foreach($grosses as $gross) {
            $revenue += ($gross['quantity'] * $gross['unit_price']);
        }

        return $revenue;
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
}