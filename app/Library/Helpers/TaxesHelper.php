<?php

namespace App\Library\Helpers;

//Internal Library
use Carbon\Carbon;

//Models
use App\Models\User\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Finances\AllianceWalletJournal;
use App\Models\MiningTax\Invoice;
use App\Models\MoonRental\AllianceMoonRental;

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

    public function GetMoonRentalTaxesGross($start, $end) {
        $revenue = 0.00;

        $revenue = AllianceMoonRental::whereBetween('rental_start', [$start, $end])
                                     ->sum('rental_amount');

        return $revenue;
    }

    public function GetMoonMiningTaxesGross($start, $end) {
        $revenue = 0.00;

        $revenue = Invoice::where([
            'status' => 'Paid',
        ])->whereBetween('date_issued', [$start, $end])
          ->sum('invoice_amount');

        return $revenue;
    }

    public function GetMoonMiningTaxesLateGross($start, $end) {
        $revenue = 0.00;

        $revenue = Invoice::where([
            'status' => 'Paid Late',
        ])->whereBetween('date_issued', [$start, $end])->sum('invoice_amount');

        return $revenue;
    }

    public function GetAllianceMarketGross($start, $end) {
        $revenue = 0.00;

        $revenue = AllianceWalletJournal::where([
            'ref_type' => 'brokers_fee',
        ])->whereBetween('date', [$start, $end])
          ->sum('amount');

        return $revenue;
    }

    public function GetJumpGateGross($start, $end) {
        $revenue = 0.00;

        $revenue = AllianceWalletJournal::where([
            'ref_type' => 'structure_gate_jump', 
            ])->whereBetween('date', [$start, $end])
              ->sum('amount');

        return $revenue;
    }

    public function GetIndustryGross($start, $end) {
        $revenue = 0.00;

        $revenue = AllianceWalletJournal::where([
            'ref_type' => 'industry_job_tax', 
            ])->whereBetween('date', [$start, $end])
              ->sum('amount');

        return $revenue;
    }

    public function GetReprocessingGross($start, $end) {
        $revenue = 0.00;

        $revenue = AllianceWalletJournal::where([
            'ref_type' => 'reprocessing_tax',
            ])->whereBetween('date', [$start, $end])
              ->sum('amount');

        return $revenue;
    }

    public function GetPIGross($start, $end) {
        $revenueImport = 0.00;
        $revenueExport = 0.00;

        //Get the import revenue from the database
        $revenueImport = AllianceWalletJournal::where([
            'ref_type' => 'planetary_import_tax',
            ])->whereBetween('date', [$start, $end])
              ->sum('amount');

        //Get the export revenue from the database      
        $revenueExport = AllianceWalletJournal::where([
            'ref_type' => 'planetary_export_tax',
            ])->whereBetween('date', [$start, $end])
              ->sum('amount');

        //Total up the two values
        $finalRevenue = $revenueImport + $revenueExport;

        //Return the values
        return $finalRevenue;
    }

    public function GetOfficeGross($start, $end) {
        $revenue = 0.00;

        $revenue = AllianceWalletJournal::where([
            'ref_type' => 'office_rental_fee', 
            ])->whereBetween('date', [$start, $end])
              ->sum('amount');

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