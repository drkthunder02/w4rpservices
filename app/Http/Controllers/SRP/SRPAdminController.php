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
use App\Models\User\User;
use App\Models\SRP\SrpFleetType;
use App\Models\SRP\SrpShipType;
use App\Models\SRP\SrpPayout;

class SRPAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:srp.admin');
    }

    public function displaySRPRequests() {
        $this->middleware('permission:srp.admin');

        $requests = array();

        $shipTypes = SrpShipType::all();
        $fleetTypes = SrpFleetType::all();
        $payouts = SrpPayout::all();
        
        $count = SRPShip::where(['approved' => 'Under Review'])->count();
        if($count === 0) {
            $requests = null;
        } else {
            $reqs = SRPShip::where(['approved' => 'Under Review'])->get()->toArray();
            foreach($reqs as $r) {
                $temp['id'] = $r['id'];
                $temp['created_at'] = $r['created_at'];
                $temp['character_name'] = $r['character_name'];
                $temp['fleet_commander_name'] = $r['fleet_commander_name'];
                $temp['zkillboard'] = $r['zkillboard'];
                $temp['loss_value'] = number_format($r['loss_value'], 2, '.', ',');
                //Get the ship type
                foreach($shipTypes as $s) {
                    if($r['ship_type'] == $s->code) {
                        $temp['ship_type'] = $s->description;
                    }
                }
                //Get the fleet type
                foreach($fleetTypes as $f) {
                    if($r['fleet_type'] == $f->code) {
                        $temp['fleet_type'] = $f->description;
                    }
                }
                //Calculate the recommended srp amount
                foreach($payouts as $p) {
                    if($r['ship_type'] == $p->code) {
                        $temp['actual_srp'] = $r['loss_value'] * ($p->payout / 100.00 );
                        $temp['actual_srp'] = number_format($temp['actual_srp'], 2, '.', ',');
                    }
                }
                
                array_push($requests, $temp);
            }
        }



        return view('srp.admin.process')->with('requests', $requests);
    }

    public function processSRPRequest() {
        $this->middleware('permission:srp.admin');

        $this->validate($request, [
            'id' => 'required',
            'approved' => 'required',
            'paid_value' => 'required',
        ]);

        if($request->notes != null) {
            $srp = SRPShip::where(['id' => $id])->update([
                'approved' => $request->approved,
                'paid_value' => $request->paid_value,
                'paid_by_id' => auth()->user()->character_id,
                'paid_by_name' => auth()->user()->name,
                'notes' => $request->notes,
            ]);
        } else {
            $srp = SRPShip::where(['id' => $id])->update([
                'approved' => $request->approved,
                'paid_value' => $request->paid_value,
                'paid_by_id' => auth()->user()->character_id,
                'paid_by_name' => auth()->user()->name,
            ]);
        }

        

        if($request->approved == 'Yes') {
            return redirect('/srp/admin/display')->with('success', 'SRP Marked as Paid');
        } else {
            return redirect('/srp/admin/display')->with('error', 'SRP Request Denied.');
        }
    }

    public function displayStatistics() {
        return view('srp.admin.statistics');
    }
}
