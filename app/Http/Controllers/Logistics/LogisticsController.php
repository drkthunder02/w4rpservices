<?php

namespace App\Http\Controllers\Logistics;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;

//Models
use App\Models\Contracts\EveContract;

//Library
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

class LogisticsController extends Controller
{
    /**
     * Function to display available contracts
     */
    public function displayLogisticsContracts() {
        //Get the non-accepted contracts
        $open = EveContract::where([
            'status' => 'outstanding',
        ])->get();

        $inProgress = EveContract::where([
            'status' => 'in_progress',
        ])->get();

        $cancelled = EveContract::where([
            'status' => 'cancelled',
        ])->get();

        $failed = EveContract::where([
            'status' => 'failed',
        ])->get();

        $deleted = EveContract::where([
            'status' => 'deleted',
        ])->get();

        $finished = EveContract::where([
            'status' => 'finished',
        ])->get();

        return view('logistics.display.contracts')->with('open', $open)
                                                  ->with('inProgress', $inProgress)
                                                  ->with('cancelled', $cancelled)
                                                  ->with('failed', $failed)
                                                  ->with('deleted', $deleted)
                                                  ->with('finished', $finished);
    }

    /**
     * Function to display current contracts user holds
     */
    public function displayUserContracts() {

    }

    /**
     * Function to calculate details needing to be set for contracts
     */
    public function displayContractForm() {

    }

    /**
     * Function to calculate details needing to be set for contracts
     */
    public function displayContractDetails(Request $request) {
        $this->validate($request, [
            'start_location',
            'end_location',
            'type',
            'collateral',  
        ]);

        
    }
}
