<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Library\Taxes\TaxesHelper;

class TaxesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
        $this->middleware('permission:admin.finance');
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
                'gross' => number_format($tHelper->GetPIGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $industrys[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetIndustryGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $reprocessings[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetReprocessingGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $offices[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetOfficeGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $markets[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMarketGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $jumpgates[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetJumpGateGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $pitransactions[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetPiGross($date['start'], $date['end']), 2, ".", ","),
            ]
        }

        //Return the view with the compact variable list
        return view('/taxes/admin/displaystreams')->with('pis', $pis)
                                           ->with('industrys', $industrys)
                                           ->with('reprocessings', $reprocessings)
                                           ->with('offices', $offices)
                                           ->with('markets', $markets)
                                           ->with('jumpgates', $jumpgates)
                                           ->with('pigross', $pitransactions);
    }
}
