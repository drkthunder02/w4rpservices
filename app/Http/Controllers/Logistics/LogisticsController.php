<?php

namespace App\Http\Controllers\Logistics;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;

//Models
use App\Models\Contracts\EveContract;

//Library
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

class LogisticsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Function to display available contracts
     */
    public function displayLogisticsContracts() {
        $this->middelware('permissions:logistics.courier');

        //Get the non-accepted contracts
        $openCount == EveContract::where(['status' => 'outstanding'])->count();
        $open = EveContract::where([
            'status' => 'outstanding',
        ])->get();
        
        $inProgressCount = EveContract::where(['status' => 'in_progress'])->count();
        $inProgress = EveContract::where([
            'status' => 'in_progress',
        ])->get();

        $finishedCount = EveContract::where(['status' => 'finished'])->count();
        $finished = EveContract::where([
            'status' => 'finished',
        ])->get();

        $totalCount = $openCount + $inProgressCount + $finishedCount;

        //Fuel Gauge Chart Declarations
        $lava = new Lavacharts;

        $openChart = $lava->DataTable();
        $openChart->addStringColumn('Contracts')
                   ->addNumberColumn('Number')
                   ->addRow(['Open Contracts', $openCount]);

        $lava->GaugeChart('Open Contracts', $openChart, [
            'width' => 300,
            'greenFrom' => 0,
            'greenTo' => 10,
            'yellowFrom' => 10,
            'yellowTo', 30,
            'redFrom' => 30,
            'redTo' => 100,
            'majorTicks' => [
                'Normal',
                'Delayed',
                'Backed Up',
            ],
        ]);


        return view('logistics.display.contracts')->with('open', $open)
                                                  ->with('inProgress', $inProgress)
                                                  ->with('finished', $finished)
                                                  ->with('openCount', $openCount)
                                                  ->with('inProgressCount', $inProgressCount)
                                                  ->with('finishedCount', $finishedCount)
                                                  ->with('totalCount', $totalCount)
                                                  ->with('lava', $lava);
    }

    /**
     * Function to calculate details needing to be set for contracts
     */
    public function displayContractForm() {
        //Declare some variables
        $route = array();
        
        //Get the distances table for solar system routes for logistics.
        $distances = SolarSystemDistance::all();

        foreach($distance as $dist) {
            $name = $dist->start_name . ' -> ' . $dist->end_name;
            
            $tempRoute = [
                'name' => $name,
                'start' => $dist->start_name,
                'end' => $dist->end_name,
            ];

            array_push($route, $tempRoute);
        }

        return view('logistics.display.courierform')->with('route', $route);
    }

    /**
     * Function to calculate details needing to be set for contracts
     */
    public function displayContractDetails(Request $request) {
        $this->validate($request, [
            'route',
            'volume',
            'collateral',  
        ]);

        
    }
}
