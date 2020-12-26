<?php

namespace App\Http\Controllers\Dashboard;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Support\Facades\Auth;

//Libraries
use App\Library\Taxes\TaxesHelper;
use App\Library\Wiki\WikiHelper;
use App\Library\Lookups\LookupHelper;
use App\Library\SRP\SRPHelper;

//Models


class AdminDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    /**
     * Show the administration dashboard.
     */
    public function displayAdminDashboard() {
        if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('moon.admin') || auth()->user()->hasPermission('srp.admin') || auth()->user()->hasPermission('contract.admin')) {
            //Do nothing and continue on
        } else {
            redirect('/dashboard');
        }

        //Declare variables we will need
        $tHelper = new TaxesHelper();
        $srpHelper = new SRPHelper();
        $days = 30;
        $months = 1;
        $lava = new Lavacharts;

        //Get the dates for the information being requested
        $dates = $tHelper->GetTimeFrameInMonths($months);

        //Get the data for the alliance income for a graph
        $pi = $tHelper->GetPIGross($dates['start'], $dates['end']);
        $industry = $tHelper->GetIndustryGross($dates['start'], $dates['end']);
        $reprocessing = $tHelper->GetReprocessingGross($dates['start'], $dates['end']);
        $office = $tHelper->GetOfficeGross($dates['start'], $dates['end']);
        $market = $tHelper->GetAllianceMarketGross($dates['start'], $dates['end']);
        $gate = $tHelper->GetJumpGateGross($dates['start'], $dates['end']);

        //Get the data for the sov expenses for a graph

        //Setup the charts
        //Setup the chart to be able the show the categories for income
        //This will be a pi-chart
        
        $iChart = $lava->DataTable();
        $iChart->addStringColumn('Categories')
               ->addNumberColumn('ISK')
               ->addRow(['pi', $pi])
               ->addRow(['industry', $industry])
               ->addRow(['reprocessing', $reprocessing])
               ->addRow(['offices', $office])
               ->addRow(['market', $market])
               ->addRow(['gate', $gate]);

        Lava::PieChart('Income', $iChart, [
            'title' => 'Alliance Income',
            'is3D' => true,
        ]);
        

        //Setup the chart to be able to show the categories for expenses
        //This will be a pi-chart
        /*
        $eChart = $lava->DataTable();
        $eCjart->addStringColumn('Categories')
               ->addNumberColumn('ISK')
               ->addRow(['sov', $sovBills])
               ->addRow(['srp', $srpActual])
               ->addRow(['maintenance', $maintenance])
               ->addRow(['wardecs', $wardecs])
               ->addRow(['fcs', $fcs])
               ->addRow(['keepstar_fuel', $keepstarFuel])
               ->addRow(['fortizar_fuel', $fortizarFuel])
               ->addRow(['astrahus_fuel', $astrahusFuel])
               ->addRow(['sotiyo_fuel', $sotiyoFuel])
               ->addRow(['azbel_fuel', $azbelFuel])
               ->addRow(['raitaru_fuel', $raitaruFuel])
               ->addRow(['beacon_fuel', $beaconFuel])
               ->addRow(['bridge_fuel', $bridgeFuel])
               ->addRow(['jammer_fuel', $jammerFuel]);
        */
        return view('admin.dashboards.dashboard');
    }
}
