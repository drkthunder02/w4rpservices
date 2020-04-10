<?php

namespace App\Http\Controllers\SRP;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

//User Libraries

//Models
use App\Models\SRP\SRPShip;
use App\Models\User\User;
use App\Models\SRP\SrpFleetType;
use App\Models\SRP\SrpShipType;
use App\Models\User\UserAlt;
use App\Models\SRP\SrpPayout;

class SRPController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displaySrpHistory() {
        
    }

    public function displayPayoutAmounts() {
        $payouts = array();
        $count = 0;
        $shipType = SrpShipType::all();
        $srpPayout = SrpPayout::all();
       
        foreach($shipType as $ship) {
            //Don't process if the code is None
            if($ship->code != 'None') {
                $tempCode = $ship->code;
                $tempDescription = $ship->description;
                $temp = SrpPayout::where(['code' => $ship->code])->first();
                
                $tempPayout = $temp->payout;

                $block = [
                    'code' => $tempCode,
                    'description' => $tempDescription,
                    'payout' => $tempPayout,
                ];

                array_push($payouts, $block);
            }            
        }

        return view('srp.payouts')->with('payouts', $payouts);
    }

    public function displaySrpForm() {
        $shipTypes = array();
        $fleetTypes = array();
        $characters = array();
        $temp = array();
        $alts = null;

        //Get all the ship types
        $shipTypesTemp = SrpShipType::all();

        //Get all of the fleet types
        $fleetTypesTemp = SrpFleetType::all();

        //Process the ship types and store in the array
        foreach($shipTypesTemp as $type) {
            $shipTypes[$type->code] = $type->description;
        }

        //Process the fleet types and store in the array
        foreach($fleetTypesTemp as $type) {
            $fleetTypes[$type->code] = $type->description;
        }

        //Get the user id and name, and store in the array
        $characters[auth()->user()->character_id] = auth()->user()->getName(); 

        //Get the alts and store in the array
        $altCount = UserAlt::where(['main_id' => auth()->user()->character_id])->count();
        if($altCount > 0) {
            $alts = UserAlt::where([
                'main_id' => auth()->user()->character_id,
            ])->get();

            foreach($alts as $alt) {
                $characters[$alt->character_id] = $alt->name;
            }
        }
        
        return view('srp.srpform')->with('fleetTypes', $fleetTypes)
                                  ->with('shipTypes', $shipTypes)
                                  ->with('characters', $characters);
    }    

    public function storeSRPFile(Request $request) {
        $name = null;

        $this->validate($request, [
            'character' => 'required',
            'FC' => 'required',
            'FleetType' => 'required',
            'zKillboard' => 'required',
            'LossValue' => 'required',
            'ShipType' => 'required',
        ]);
            
        //See if the FC Name ties to a user on the services site
        $fcId = User::where(['name' => $request->FC])->get(['character_id']);
        
        //Take the loss value and remove ' ISK' from it.  Convert the string to a number
        //May need to work on some locale stuff here but will think about it first.
        $lossValue = str_replace(' ISK', '', $request->LossValue);
        $lossValue = str_replace(',', '', $lossValue);
        $lossValue = floatval($lossValue);

        //Convert the FC name to a regular case of characters
        $tempFcName = strtolower($request->FC);
        $tempFcName = ucwords($tempFcName);

        $userCount = User::where(['character_id' => $request->character])->count();
        $altCount = UserAlt::where(['character_id' => $request->character])->count();

        if($userCount > 0) {
            $tempUser = User::where(['character_id' => $request->character])->first();
            $name = $tempUser->name;
        } else if($altCount > 0) {
            $tempAlt = UserAlt::where(['character_id' => $request->character])->first();
            $name = $tempAlt->name;
        } else {
            $name = 'None';
        }

        $ship = new SRPShip;
        $ship->character_id = $request->character;
        $ship->character_name = $name;
        $ship->fleet_commander_name = $tempFcName;
        if(isset($fcId[0])) {
            $ship->fleet_commander_id = $fcId[0]->character_id;
        }
        $ship->zkillboard = $request->zKillboard;
        $ship->fleet_type = $request->FleetType;
        $ship->ship_type = $request->ShipType;
        $ship->loss_value = $lossValue;
        $ship->save();

        return redirect('/dashboard')->with('success', 'SRP Form Submitted.');
    }

}
