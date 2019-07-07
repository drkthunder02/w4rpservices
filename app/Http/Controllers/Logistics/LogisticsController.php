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

        $cancelledCount = EveContract::where(['status' => 'cancelled'])->count();
        $cancelled = EveContract::where([
            'status' => 'cancelled',
        ])->get();

        $failedCount = EveContract::where(['status' => 'failed'])->count();
        $failed = EveContract::where([
            'status' => 'failed',
        ])->get();

        $deletedCount = EveContract::where(['status' => 'deleted'])->count();
        $deleted = EveContract::where([
            'status' => 'deleted',
        ])->get();

        $finishedCount = EveContract::where(['status' => 'finished'])->count();
        $finished = EveContract::where([
            'status' => 'finished',
        ])->get();

        $totalCount = $openCount + $inProgressCount + $cancelledCoutn + $failedCount + $deletedCount + $finishedCount;

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
                                                  ->with('cancelled', $cancelled)
                                                  ->with('failed', $failed)
                                                  ->with('deleted', $deleted)
                                                  ->with('finished', $finished)
                                                  ->with('openCount', $openCount)
                                                  ->with('inProgressCount', $inProgressCount)
                                                  ->with('cancelledCount', $cancelledCount)
                                                  ->with('failedCount', $failedCount)
                                                  ->with('deletedCount', $deletedCount)
                                                  ->with('finishedCount', $finishedCount)
                                                  ->with('totalCount', $totalCount)
                                                  ->with('lava', $lava);
    }

    /**
     * Function to calculate details needing to be set for contracts
     */
    public function displayContractForm() {
        $distances = SolarSystemDistance::all();

        return view('logistics.display.courierform');
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
