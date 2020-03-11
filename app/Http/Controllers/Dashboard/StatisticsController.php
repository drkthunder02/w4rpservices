<?php

namespace App\Http\Controllers\Dashboard;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;

//Libraries
use App\Library\Taxes\TaxesHelper;
use App\Library\Lookups\LookupHelper;
use App\Library\SRP\SRPHelper;

//Models
use App\Models\User\User;
use App\Models\Doku\DokuGroupNames;
use App\Models\Doku\DokuMember;
use App\Models\Doku\DokuUser;
use App\Models\SRP\SrpFleetType;
use App\Models\SRP\SRPShip;
use App\Models\SRP\SrpShipType;
use App\Models\SRP\SrpPayout;


class StatisticsController extends Controller
{
    /**
     * Create a new controller instance
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display Jump Bridge Statistics
     */
    public function displayJumpBridgeStatistics() {
        $this->middleware('role:Admin');

        $lava = new Lavacharts;
        
    }

    /**
     * Display Taxes Statistics
     */
    public function displayTaxes() {
        $this->middleware('role:Admin');

        //Declare variables needed for displaying items on the page
        $months = 3;
        $pi = array();
        $industry = array();
        $reprocessing = array();
        $office = array();
        $corpId = 98287666;
        $srpActual = array();
        $srpLoss = array();

        /** Taxes Pane */
        //Declare classes needed for displaying items on the page
        $tHelper = new TaxesHelper;
        $srpHelper = new SRPHelper;
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
        }

        return view('admin.dashboards.taxes')->with('pis', $pis)
                                            ->with('industrys', $industrys)
                                            ->with('offices', $offices)
                                            ->with('markets', $markets)
                                            ->with('jumpgates', $jumpgates)
                                            ->with('reprocessings', $reprocessings)
                                            ->with('srpActual', $srpActual)
                                            ->with('srpLoss', $srpLoss);
    }

    /**
     * Display wiki statistics
     */
    public function displayWikiStatistics() {
        $this->middleware('role:Admin');


    }

    /**
     * Display SRP Statistics
     */
    public function displaySRPStatistics() {
        $months = 3;
        $barChartData = array();
        $start = Carbon::now()->toDateTimeString();
        $end = Carbon::now()->subMonths(1)->toDateTimeString();

        //Declare the Lavacharts variable
        $lava = new Lavacharts;

        //We need a function from this library rather than recreating a new library
        $srpHelper = new SRPHelper();

        /**
         * Pie chart for the number of approved, denied, and under review payouts currently in the system.
         */
        //Get the count of open srp requests
        $pieOpen = SRPShip::where([
            'approved' => 'Under Review',
            ['created_at', '>=', $end],
            ])->count();
        //Get the count of approved srp requests
        $pieApproved = SRPShip::where([
            'approved' => 'Approved',
            ['created_at', '>=', $end],
            ])->count();
        //Get the count of denied srp requests
        $pieDenied = SRPShip::where([
            'approved' => 'Denied',
            ['created_at', '>=', $end],
            ])->count();

        //Create a new datatable for the lavachart.
        $srp = $lava->DataTable();
        //Add string columns, number columns, and data rows for the chart
        $srp->addStringColumn('ISK Value')
                ->addNumberColumn('ISK')
                ->addRow(['Approved', $pieApproved])
                ->addRow(['Denied', $pieDenied])
                ->addRow(['Under Review', $pieOpen]);
        //Create the pie chart in memory with any options needed to render the chart
        $lava->PieChart('SRP Stats', $srp, [
            'title'  => 'SRP Stats',
            'is3D'   => true,
        ]);
        
        /**
         * Gauage chart for showing number of open srp requests
         */
        //Create a new datatable in the 
        $adur = $lava->DataTable();
        //Add string columns, number columns, and data row for the chart
        $adur->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Under Review', $pieOpen]);
        //Create the gauge chart with any options needed to render the chart
        $lava->GaugeChart('SRP', $adur, [
            'width'      => 400,
            'greenFrom'  => 0,
            'greenTo'    => 20,
            'yellowFrom' => 20,
            'yellowTo'   => 40,
            'redFrom'    => 40,
            'redTo'      => 100,
            'majorTicks' => [
                'Safe',
                'Critical',
            ],
        ]);

        /**
         * Create a vertical chart of all of the cost codes for the ships being SRP'ed.
         * The chart will be by cost code of ships being replaced
         */
        //Declare the data table
        $costCodeChart = $lava->DataTable();

        //Get the approved, under review, and denied cost codes and amounts
        $t1fdcApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1FDC', 
        ])->sum('paid_value');
        $t1fdcUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1FDC',
        ])->sum('loss_value');
        $t1fdcDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1FDC',
        ])->sum('loss_value');

        $t1bcApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1BC',
        ])->sum('paid_value');
        $t1bcUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1BC',
        ])->sum('loss_value');
        $t1bcDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1BC',
        ])->sum('loss_value');
        
        $t2fdApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T2FD',
        ])->sum('paid_value');
        $t2fdUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T2FD',
        ])->sum('loss_value');
        $t2fdDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T2FD',
        ])->sum('loss_value');

        $t3dApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T3D',
        ])->sum('paid_value');
        $t3dUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T3D',
        ])->sum('loss_value');
        $t3dDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T3D',
        ])->sum('loss_value');

        $t1t2logiApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1T2Logi',
        ])->sum('paid_value');
        $t1t2logiUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1T2Logi',
        ])->sum('loss_value');
        $t1t2logiDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1T2Logi',
        ])->sum('loss_value');

        $reconsApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'REC',
        ])->sum('paid_value');
        $reconsUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'REC',
        ])->sum('loss_value');
        $reconsDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'REC',
        ])->sum('loss_value');

        $t2cApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T2C',
        ])->sum('paid_value');
        $t2cUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T2C',
        ])->sum('loss_value');
        $t2cDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T2C',
        ])->sum('loss_value');

        $t3cApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T3C',
        ])->sum('paid_value');
        $t3cUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T3C',
        ])->sum('loss_value');
        $t3cDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T3C',
        ])->sum('loss_value');

        $commandApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'COM',
        ])->sum('paid_value');
        $commandUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'COM',
        ])->sum('loss_value');
        $commandDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'COM',
        ])->sum('loss_value');

        $interdictorApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'INTD',
        ])->sum('paid_value');
        $interdictorUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'INTD',
        ])->sum('loss_value');
        $interdictorDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'INTD',
        ])->sum('loss_value');

        $t1bsApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1BS',
        ])->sum('paid_value');
        $t1bsUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1BS',
        ])->sum('loss_value');
        $t1bsDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1BS',
        ])->sum('loss_value');

        $dksApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'DKS',
        ])->sum('paid_value');
        $dksUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'DKS',
        ])->sum('loss_value');
        $dksDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'DKS',
        ])->sum('loss_value');


        //Add string column, number columns.
        $costCodeChart->addStringColumn('SRP Costs')
                      ->addNumberColumn('Approved')
                      ->addNumberColumn('Under Review')
                      ->addNumberColumn('Denied')
                      ->addRow(['T1FDC', $t1fdcApproved, $t1fdcUnderReview, $t1fdcDenied])
                      ->addRow(['T1BC', $t1bcApproved, $t1bcUnderReview, $t1bcDenied])
                      ->addRow(['T1BS', $t1bsApproved, $t1bsUnderReview, $t1bsDenied])
                      ->addRow(['T2FD', $t2fdApproved, $t2fdUnderReview, $t2fdDenied])
                      ->addRow(['T2C', $t2cApproved, $t2cUnderReview, $t2cDenied])
                      ->addRow(['T1T2Logi', $t1t2logiApproved, $t1t2logiUnderReview, $t1t2logiDenied])
                      ->addRow(['T3D', $t3dApproved, $t3dUnderReview, $t3dDenied])
                      ->addRow(['T3C', $t3cApproved, $t3cUnderReview, $t3cDenied])
                      ->addRow(['RECON', $reconsApproved, $reconsUnderReview, $reconsDenied])
                      ->addRow(['COMMAND', $commandApproved, $commandUnderReview, $commandDenied])
                      ->addRow(['DKS', $dksApproved, $dksUnderReview, $dksDenied]);
        
        $lava->ColumnChart('Cost Codes', $costCodeChart, [
            'columns' => 4,
            'title' => 'Cost Code SRP Chart',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
            ],
        ]);  

        return view('srp.admin.statistics')->with('lava', $lava);
    }
}
