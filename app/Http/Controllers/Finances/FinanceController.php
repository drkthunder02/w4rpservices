<?php

namespace App\Http\Controllers\Finances;

//Internal Libraries
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use Khill\Lavacharts\Lavacharts;

//Application Library
use App\Library\Helpers\TaxesHelper;
use App\Library\Helpers\LookupHelper;
use App\Library\Helpers\SRPHelper;

//Models
use App\Models\User\User;

class FinanceController extends Controller
{
    //Construct
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:ceo');
    }

    /**
     * Display the finances of the alliance with cards like the admin dashboard
     */
    public function displayCards() {
        $months = 3;

        $pi = array();
        $industry = array();
        $reprocessing = array();
        $office = array();
        $corpId = 98287666;
        $srpActual = array();
        $srpLoss = array();
        $miningTaxes = array();
        $miningTaxesLate = array();

        /** Taxes Pane */
        //Declare classes needed for displaying items on the page
        $tHelper = new TaxesHelper();
        $srpHelper = new SRPHelper();
        //Get the dates for the tab panes
        $dates = $tHelper->GetTimeFrameInMonths($months);

        //Get the data for the Taxes Pane
        foreach($dates as $date) {
            //Get the srp actual pay out for the date range
            $srpActual[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($srpHelper->GetAllianceSRPActual($date['start'], $date['end']), 2, ".", ","),
            ];

            //Get the srp loss value for the date range
            $srpLoss[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($srpHelper->GetAllianceSRPLoss($date['start'], $date['end']), 2, ".", ","),
            ];

            //Get the pi taxes for the date range
            $pis[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetPIGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the industry taxes for the date range
            $industrys[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetIndustryGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the reprocessing taxes for the date range
            $reprocessings[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetReprocessingGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the office taxes for the date range
            $offices[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetOfficeGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the market taxes for the date range
            $markets[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetAllianceMarketGross($date['start'], $date['end']), 2, ".", ","),
            ];
            //Get the jump gate taxes for the date range
            $jumpgates[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetJumpGateGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $miningTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMoonMiningTaxesGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $miningTaxesLate[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMoonMiningTaxesLateGross($date['start'], $date['end']), 2, ".", ","),
            ];

            $moonRentalTaxes[] = [
                'date' => $date['start']->toFormattedDateString(),
                'gross' => number_format($tHelper->GetMoonRentalTaxesGross($date['start'], $date['end']), 2, ".", ","),
            ];


        }

        return view('finances.display.card')->with('pis', $pis)
                                            ->with('industrys', $industrys)
                                            ->with('offices', $offices)
                                            ->with('markets', $markets)
                                            ->with('jumpgates', $jumpgates)
                                            ->with('reprocessings', $reprocessings)
                                            ->with('srpActual', $srpActual)
                                            ->with('srpLoss', $srpLoss)
                                            ->with('miningTaxes', $miningTaxes)
                                            ->with('miningTaxesLate', $miningTaxesLate)
                                            ->with('moonRentalTaxes', $moonRentalTaxes);
    }

    /**
     * Display a graph of the financial outlook of the alliance
     */
    public function displayOutlook() {
        $months = 6;
        $income = array();
        $expenses = array();

        /**
         * Declare classes needed for displaying items on the page
         */
        $tHelper = new TaxesHelper();
        $srpHelper = new SRPHelper();
        //Get the dates to process
        $dates = $tHelper->GetTimeFrameInMonths($months);

        /**
         * Setup the chart variables
         */
        $lava = new Lavacharts;
        $finances = $lava->DataTable();

        $finances->addDateColumn('Month')
                 ->addNumberColumn('Income')
                 ->addNumberColumn('Expenses')
                 ->addNumberColumn('Difference')
                 ->setDateTimeFormat('Y');

        /**
         * Get the income and expenses data for date range
         */
        foreach($dates as $date) {
            /**
             * Get the individual expenses. 
             * Will totalize later in the foreach loop
             */
            $srpActual = $srpHelper->GetAllianceSRPActual($date['start'], $date['end']);
            $capEx = 0.00;
            $sovExpenses = 3000000000.00

            /**
             * Get the individual incomes.
             * Will totalize later in the foreach loop
             */
            $pi = $tHelper->GetPIGross($date['start'], $date['end']);
            $industry = $tHelper->GetIndustryGross($date['start'], $date['end']);
            $reprocessing = $tHelper->GetReprocessingGross($date['start'], $date['end']);
            $offices = $tHelper->GetOfficeGross($date['start'], $date['end']);
            $market = $tHelper->GetAllianceMarketGross($date['start'], $date['end']);
            $jumpgate = $tHelper->GetJumpGateGross($date['start'], $date['end']);
            $miningTaxes = $tHelper->GetMoonMiningTaxesGross($date['start'], $date['end']) + $tHelper->GetMoonMiningTaxesLateGross($date['start'], $date['end']);
            $moonRentals = $tHelper->GetMoonRentalTaxesGross($date['start'], $date['end']);

            /**
             * Totalize the expenses
             */
            $expenses = (($srpActual + $capEx + $sovExpenses) / 1000000.00);
            
            /**
             * Totalize the incomes
             */
            $incomes = (($pi +
                         $industry +
                         $reprocessing +
                         $offices +
                         $market +
                         $jumpgate +
                         $miningTaxes +
                         $moonRentals) / 1000000.00);

            /**
             * Get the difference between income and expenses
             */
            $difference = $incomes - $expenses;

            $finances->addRow([$date['start'], $incomes, $expenses, $difference]);
        }

        /**
         * Finish setting up the lava chart before passing it to the blade template
         */
        $lava->ComboChart('Finances', $finances, [
            'title' => 'Alliance Finances',
            'titleTextStyle' => [
                'color' => 'rgb(123, 65, 80)',
                'fontSize' => 16,
            ],
            'legend' => [
                'position' => 'in',
            ],
            'seriesType' => 'bars',
            'series' => [
                2 => [
                    'type' => 'line',
                ],
            ],
        ]);

        return view('finances.display.outlook')->with('lava', $lava);
    }

    /**
     * Request an amount of ISK to fund a capital project
     */
    public function requestFundingDisplay() {

    }

    /**
     * Store the request for the capital project
     */
    public function storeFundingRequest() {

    }

    /**
     * Delete a request for the capital project
     */
    public function deleteFundingRequest() {

    }
}
