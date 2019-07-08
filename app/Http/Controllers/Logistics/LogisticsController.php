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
        $route = LogisticsRoute::pluck('name');

        return view('logistics.display.courierform')->with('route', $route);
    }

    /**
     * Function to calculate details needing to be set for contracts
     */
    public function displayContractDetails(Request $request) {
        $startSystem = null;
        $endSystem = null;
        $reward = null;
        $okVolume = null;
        $corporation = null;

        $this->validate($request, [
            'route' => 'required',
            'volume' => 'required',
            'collateral' => 'required',  
        ]);

        //Sanitize the collateral string as we want
        $collateral = str_replace(' ISK', '', $request->collateral);
        $collateral = str_replace(',', '', $collateral);
        $collateral = floatval($collateral);
        
        $volume = $request->volume;

        $route = LogisticsRoute::where([
            'name'=> $request->route,
        ])->get();

        if($routeCount == 0) {
            $startSystem = 'N/A';
            $endSystem = 'N/A';
        } else {
            //Check the volume of the contract
            if($volume > $route->max_size) {
                $okVolume = false;
            } else {
                $okVolume = true;
            }

            //Compose the route to be displayed
            $tempSystem = explode(' -> ', $request->route);
            $startSystem = $tempSystem[0];
            $endSystem = $tempSystem[1];

            if($startSystem == 'Jita' || $endSystem == 'Jita') {
                $corporation = 'Infernal Armada';
            } else {
                $corporation = 'Inmate Logistics';
            }

            //Calculate the route parameters
            $reward = ($route->price_per_m3 * $volume) + ( $collateral * 1.02);
        }

        return view('logistics.display.courier')->with('okVolume', $okVolume)
                                                ->with('collateral', $collateral)
                                                ->with('reward', $reward)
                                                ->with('startSystem', $startSystem)
                                                ->with('endSystem', $endSystem)
                                                ->with('corporation', $corporation);
    }
}
