<?php

namespace App\Http\Controllers\SRP;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

//User Libraries

//Models
use App\Models\SRP\Fleet;
use App\Models\SRP\FleetCommander;
use App\Models\SRP\Ship;

class SRPController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middelware('role:User');
    }

    public function displaySrpForm() {
        return view('srp.srpform');
    }    

    public function storeSRPFile() {
        $this->validate($request, [
            'FC' => 'required',
            'FleetType' => 'required',
            'zKillboard' => 'required',
            'LossValue' => 'required',
            'ShipType' => 'required',
        ]);

        $fc = $request->FC;
        $fleetType = $request->FleetType;
        $zKill = $request->zKillboard;
        $loss = $request->LossValue;
        $ship = $request->ShipType;
    }

    public function displaySRPRequests() {
        $this->middleware('permission:SRP');


    }
}
