<?php

namespace App\Http\Controllers\SRP;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

//User Libraries

//Models
use App\Models\SRP\SRPShip;

class SRPController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displaySrpForm() {
        return view('srp.srpform');
    }    

    public function storeSRPFile(Request $request) {
        $this->validate($request, [
            'FC' => 'required',
            'FleetType' => 'required',
            'zKillboard' => 'required',
            'LossValue' => 'required',
            'ShipType' => 'required',
        ]);

        //See if the FC Name ties to a user on the services site
        $fcId = User::where(['name' => $request->fc])->get(['character_id']);

        $ship = new SRPShip;
        $ship->character_id = auth()->user()->character_id;
        $ship->character_name = auth()->user()->name;
        $ship->fleet_commander_name = $request->FC;
        if($fcId[0] != null) {
            $ship->fleet_commander_id = $fcId[0];
        }
        $ship->zkillboard = $request->zKillboard;
        $ship->ship_type = $request->ShipType;
        $ship->loss_value = $request->LossValue;
        $ship->save();

        return redirect('/srpform')->with('success', 'SRP Form Submitted.');
    }

}
