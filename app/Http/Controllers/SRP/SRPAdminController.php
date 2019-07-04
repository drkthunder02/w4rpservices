<?php

namespace App\Http\Controllers\SRP;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Khill\Lavacharts\Lavacharts;
use Carbon\Carbon;

//User Libraries
use App\Library\SRP\SRPHelper;

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
                $temp['loss_value'] = $r['loss_value'];
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
                    }
                }
                
                array_push($requests, $temp);
            }
        }

        return view('srp.admin.process')->with('requests', $requests);
    }

    public function processSRPRequest(Request $request) {
        $this->validate($request, [
            'id' => 'required',
            'approved' => 'required',
            'paid_value' => 'required',
        ]);

        $paidValue = str_replace(',', '', $request->paid_value);

        if($request->notes != null) {
            $srp = SRPShip::where(['id' => $request->id])->update([
                'approved' => $request->approved,
                'paid_value' => $paidValue,
                'paid_by_id' => auth()->user()->character_id,
                'paid_by_name' => auth()->user()->name,
                'notes' => $request->notes,
            ]);
        } else {
            $srp = SRPShip::where(['id' => $request->id])->update([
                'approved' => $request->approved,
                'paid_value' => $paidValue,
                'paid_by_id' => auth()->user()->character_id,
                'paid_by_name' => auth()->user()->name,
            ]);
        }

        if($request->approved == 'Approved') {
            return redirect('/srp/admin/display')->with('success', 'SRP Marked as Paid');
        } else {
            return redirect('/srp/admin/display')->with('error', 'SRP Request Denied.');
        }
    }

    public function displayStatistics() {
        $months = 3;
        $barChartData = array();
        $now = Carbon::now()->toFormattedString();
        $previous = Carbon::now()->subMonths(3)->toFormattedString();

        //We need a function from this library rather than recreating a new library
        $srpHelper = new SRPHelper();

        //Get the number of approved, denied, and under review payouts currently from the database
        $pieOpen = SRPShip::where(['approved' => 'Under Review'])
                            ->whereBetween('created_at', [$now, $previous])
                            ->count();
        $pieApproved = SRPShip::where(['approved' => 'Approved'])
                            ->whereBetween('created_at', [$now, $previous])
                            ->count();
        $pieDenied = SRPShip::where(['approved' => 'Denied'])
                            ->whereBetween('created_at', [$now, $previous])
                            ->count();
        
        dd($pieApproved);
        dd($pieDenied);
        //Get the amount of open orders
        //Just copy the data from the previous data pull
        $gaugeReview = $pieOpen;

        //Get the losses by Fleet Commander Name, and populate variables for the table
        $fcNames = SRPShip::whereBetween('created_at', [$now, $previous])
                        ->pluck('fleet_commander_name');
        foreach($fcNames as $name) {
            $total = SRPShip::where(['fleet_commander_name' => $name])
                            ->whereBetween('created_at', [$now, $previous])
                            ->sum('loss_value');
            $temp = [
                'fc' => $name,
                'total' => $total,
            ];

            array_push($barChartData, $temp);
        }

        //Pie Chart for approval, denied, and under review
        $lava = new Lavacharts; // See note below for Laravel

        $srp = $lava->DataTable();

        $srp->addStringColumn('ISK Value')
                ->addNumberColumn('ISK')
                ->addRow(['Approved', $pieApproved])
                ->addRow(['Denied', $pieDenied])
                ->addRow(['Under Review', $pieOpen]);

        $lava->PieChart('SRP Stats', $srp, [
            'title'  => 'SRP Stats',
            'is3D'   => true,
        ]);

        $adur = $lava->DataTable();

        $adur->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Under Review', $gaugeReview]);

        $lava->GaugeChart('SRP', $adur, [
            'width'      => 400,
            'greenFrom'  => 0,
            'greenTo'    => 10,
            'yellowFrom' => 11,
            'yellowTo'   => 40,
            'redFrom'    => 41,
            'redTo'      => 100,
            'majorTicks' => [
                'Safe',
                'Critical'
            ]
        ]);

        $fcs = $lava->DataTable();

        $fcs->addStringColumn('Fleet Commander Losses')
            ->addNumberColumn('ISK');
        foreach($barChartData as $data) {
            $fcs->addRow([$data['fc'], $data['total']]);
        }

        $lava->BarChart('FCs', $fcs);

        return view('srp.admin.statistics')->with('lava', $lava);
    }

    public function displayCostCodes() {
        $costcodes = array();
        $count = 0;
        $shipType = SrpShipType::all();
        $srpPayout = SrpPayout::all();
       
        foreach($shipType as $ship) {
            //Don't process if the code is None
            if($ship->code != 'None') {
                $tempCode = $ship->code;
                $tempDescription = $ship->description;
                $temp = SrpPayout::where(['code' => $ship->code])->get();
                $tempPayout = $temp->payout;

                $block = [
                    'code' => $tempCode,
                    'description' => $tempDescription,
                    'payout' => $tempPayout,
                ];

                array_push($costcodes, $block);
            }            
        }

        return view('srp.admin.costcodes.display')->with($costcodes);
    }

    public function addCostCode(Request $request) {
        $this->validate($request, [
            'code' => 'required',
            'description' => 'required',
            'payout' => 'required',
        ]);

        $code = $request->code;
        $description = $request->description;
        $payout = $request->payout;

        $payoutCount = SrpPayout::where(['code' => $code])->count();
        $shipTypeCount = SrpShipType::where(['code' => $code])->count();

        //If we don't find the cost code, let's add it.  otherwise send an error.
        if($payoutCount == 0 && $shipTypeCount == 0) {
            $payoutTable = new SrpPayout;
            $payoutTable->code = $code;
            $payoutTable->payout = $payout;
            $payoutTable->save();

            $shipType = new SrpShipType;
            $shipType->code = $code;
            $shipType->description = $description;
            $shipType->save();

            redirect('/srp/admin/display')->with('success', 'Cost code added.');
        } else {
            redirect('/srp/admin/display')->with('error', 'Cost code already exists in the database.');
        }
    }

    public function modifyCostCodes(Request $request) {
        $this->validate($request, [
            'code' => 'required',
            'description' => 'required',
            'payout' => 'required',
        ]);

        //Update the SrpShipType
        SrpShipType::where(['code' => $request->code])->update([
            'description' => $request->description,
        ]);

        //Update the payout
        SrpPayout::where(['code' => $request->code])->update([
            'payout' => $request->payout,
        ]);

        return redirect('/srp/admin/display')->with('success', 'Payout and Description updated.');
    }
}
