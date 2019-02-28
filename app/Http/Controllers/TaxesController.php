<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

use App\Library\Structures\StructureTaxHelper;
use App\Library\Taxes\TaxesHelper;

class TaxesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
        $this->middleware('permission:structure.operator');
    }

    public function displayTaxSummary() {
        $months = 3;
        $pi = array();
        $industry = array();
        $reprocessing = array();
        $office = array();
        $corpId = 98287666;

        //Declare the tax helper class
        $tHelper = new TaxesHelper();

        //Get the dates we are working with
        $dates = $tHelper->GetTimeFrameInMonths($months);

        foreach($dates as $date) {
            
            $pis[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetPIGross($start, $end), 2, ".", ","),
            ];

            $industrys[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($tHelper->GetIndustryGross($start, $end), 2, ".", ","),
            ];

            $reprocessings[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($tHelper->GetReprocessingGross($start, $end), 2, ".", ","),
            ];

            $offices[] = [
                'date' => $date['start']->toFormattedDateString(),
                'tax' => number_format($tHelper->GetOfficeGross($start, $end), 2, ".", ","),
            ];
        }

        //Return the view with the compact variable list
        return view('/taxes/admin/displaystreams')->with('pi', $pi)
                                           ->with('industry', $industry)
                                           ->with('reprocessing', $reprocessing)
                                           ->with('office', $office);

    }
}
