<?php

namespace App\Http\Controllers\SRP;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        //Middleware needed for permissions
        $this->middleware('permission:srp.admin');

        //Create the array
        $requests = array();
        $viewShipTypes = array();

        //Declare variables for use later.
        $sum_actual = 0.00;
        $sum_loss = 0.00;

        //Get the ship types from the database
        $shipTypes = SrpShipType::all();

        //Setup the viewShipTypes variable
        $tempShipTypes = SrpShipType::groupBy('code')->pluck('code');
        foreach($tempShipTypes as $key => $value) {
            $viewShipTypes[$value] = $value;
        }

        //Get the fleet types from the database
        $fleetTypes = SrpFleetType::all();

        //Get the payouts from the database
        $payouts = SrpPayout::all();
        
        //Get the SRP ship count to see how many requests are avaiable "Under Review" in the database
        $count = SRPShip::where(['approved' => 'Under Review'])->count();
        //If the count is 0 then there are no requests
        if($count === 0) {
            $requests = null;
        } else {    //Process each request
            $reqs = SRPShip::where(['approved' => 'Under Review'])->get()->toArray();
            foreach($reqs as $r) {
                $temp['id'] = $r['id'];
                $temp['created_at'] = $r['created_at'];
                $temp['character_name'] = $r['character_name'];
                $temp['fleet_commander_name'] = $r['fleet_commander_name'];
                $temp['zkillboard'] = $r['zkillboard'];
                $temp['loss_value'] = $r['loss_value'];
                $sum_loss += $temp['loss_value'];
                //Get the ship type
                foreach($shipTypes as $s) {
                    if($r['ship_type'] == $s->code) {
                        $temp['ship_type'] = $s->description;
                        $temp['cost_code'] = $s->code;
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
                        $temp['payout_percentage'] = $p->payout;
                        $sum_actual += $temp['actual_srp'];
                    }
                }
                
                //Push the calculations into the array
                array_push($requests, $temp);
            }
        }

        $sum_actual = number_format($sum_actual, 2, '.', ',');
        $sum_loss = number_format($sum_loss, 2, '.', ',');

        //Return the view with the variables
        return view('srp.admin.process')->with('requests', $requests)
                                        ->with('sum_actual', $sum_actual)
                                        ->with('sum_loss', $sum_loss)
                                        ->with('viewShipTypes', $viewShipTypes);
    }

    public function updateLossValue($id, $value) {
        //Convert the string into a decimal style number to be stored correctly
        $lossValue = str_replace(',', '', $value);
        $lossValue = floatval($lossValue);
        
        SRPShip::where(['id' => $id])->update([
            'loss_value' => $lossValue,
        ]);

        return redirect('/srp/admin/display');
    }

    public function updateShipType($id, $value) {
        SRPShip::where(['id' => $id])->update([
            'ship_type' => $value,
        ]);

        return redirect('/srp/admin/display');
    }

    public function processSRPRequest(Request $request) {
        //Validate the request
        $this->validate($request, [
            'id' => 'required',
            'approved' => 'required',
            'total_loss' => 'required',
        ]);

        //Get the total loss value from the form and convert it to the right format
        $totalLoss = str_replace(',', '', $request->total_loss);
        $totalLoss = floatval($totalLoss);

        //If the notes are not null update like this.
        if($request->notes != null) {
            $srp = SRPShip::where(['id' => $request->id])->update([
                'approved' => $request->approved,
                'paid_by_id' => auth()->user()->character_id,
                'paid_by_name' => auth()->user()->name,
                'notes' => $request->notes,
                'loos_value' => $totalLoss,
            ]);
        } else {
            $srp = SRPShip::where(['id' => $request->id])->update([
                'approved' => $request->approved,
                'paid_by_id' => auth()->user()->character_id,
                'paid_by_name' => auth()->user()->name,
                'loss_value' => $request->total_loss,
            ]);
        }

        if($request->approved == 'Approved') {
            return redirect('/srp/admin/display')->with('success', 'SRP Marked as Paid');
        } else {
            return redirect('/srp/admin/display')->with('error', 'SRP Request Denied.');
        }
    }

    public function displayHistory() {

        $srpApproved = SRPShip::where([
            'approved' => 'Approved',
        ])->orderBy('created_at', 'desc')->paginate(25);

        $srpDenied = SRPShip::where([
            'approved' => 'Denied',
        ])->orderBy('created_at', 'desc')->paginate(25);
        
        return view('srp.admin.history')->with('srpApproved', $srpApproved)
                                        ->with('srpDenied', $srpDenied);
    }

    public function displayStatistics() {
        $months = 3;
        $barChartData = array();
        $start = Carbon::now()->toDateTimeString();
        $end = Carbon::now()->subMonths(1)->toDateTimeString();

        //Declare the Lavacharts variable
        $lava = new Lavacharts;

        //We need a function from this library rather than recreating a new library
        $srpHelper = new SRPHelper();

        /**
         * Pie chart for the number of approved, denied, and under review payouts currently in the system.
         */
        //Get the count of open srp requests
        $pieOpen = SRPShip::where([
            'approved' => 'Under Review',
            ['created_at', '>=', $end],
            ])->count();
        //Get the count of approved srp requests
        $pieApproved = SRPShip::where([
            'approved' => 'Approved',
            ['created_at', '>=', $end],
            ])->count();
        //Get the count of denied srp requests
        $pieDenied = SRPShip::where([
            'approved' => 'Denied',
            ['created_at', '>=', $end],
            ])->count();

        //Create a new datatable for the lavachart.
        $srp = $lava->DataTable();
        //Add string columns, number columns, and data rows for the chart
        $srp->addStringColumn('ISK Value')
                ->addNumberColumn('ISK')
                ->addRow(['Approved', $pieApproved])
                ->addRow(['Denied', $pieDenied])
                ->addRow(['Under Review', $pieOpen]);
        //Create the pie chart in memory with any options needed to render the chart
        $lava->PieChart('SRP Stats', $srp, [
            'title'  => 'SRP Stats',
            'is3D'   => true,
        ]);
        
        /**
         * Gauage chart for showing number of open srp requests
         */
        //Create a new datatable in the 
        $adur = $lava->DataTable();
        //Add string columns, number columns, and data row for the chart
        $adur->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Under Review', $pieOpen]);
        //Create the gauge chart with any options needed to render the chart
        $lava->GaugeChart('SRP', $adur, [
            'width'      => 400,
            'greenFrom'  => 0,
            'greenTo'    => 20,
            'yellowFrom' => 20,
            'yellowTo'   => 40,
            'redFrom'    => 40,
            'redTo'      => 100,
            'majorTicks' => [
                'Safe',
                'Critical',
            ],
        ]);

        /**
         * Create a vertical chart of all of the cost codes for the ships being SRP'ed.
         * The chart will be by cost code of ships being replaced
         */
        //Declare the data table
        $costCodeChart = $lava->DataTable();

        //Get the approved, under review, and denied cost codes and amounts
        $t1fdcApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1FDC', 
        ])->sum('paid_value');
        $t1fdcUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1FDC',
        ])->sum('loss_value');
        $t1fdcDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1FDC',
        ])->sum('loss_value');

        $t1bcApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1BC',
        ])->sum('paid_value');
        $t1bcUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1BC',
        ])->sum('loss_value');
        $t1bcDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1BC',
        ])->sum('loss_value');
        
        $t2fdApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T2FD',
        ])->sum('paid_value');
        $t2fdUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T2FD',
        ])->sum('loss_value');
        $t2fdDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T2FD',
        ])->sum('loss_value');

        $t3dApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T3D',
        ])->sum('paid_value');
        $t3dUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T3D',
        ])->sum('loss_value');
        $t3dDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T3D',
        ])->sum('loss_value');

        $t1t2logiApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1T2Logi',
        ])->sum('paid_value');
        $t1t2logiUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1T2Logi',
        ])->sum('loss_value');
        $t1t2logiDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1T2Logi',
        ])->sum('loss_value');

        $reconsApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'REC',
        ])->sum('paid_value');
        $reconsUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'REC',
        ])->sum('loss_value');
        $reconsDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'REC',
        ])->sum('loss_value');

        $t2cApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T2C',
        ])->sum('paid_value');
        $t2cUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T2C',
        ])->sum('loss_value');
        $t2cDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T2C',
        ])->sum('loss_value');

        $t3cApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T3C',
        ])->sum('paid_value');
        $t3cUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T3C',
        ])->sum('loss_value');
        $t3cDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T3C',
        ])->sum('loss_value');

        $commandApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'COM',
        ])->sum('paid_value');
        $commandUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'COM',
        ])->sum('loss_value');
        $commandDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'COM',
        ])->sum('loss_value');

        $interdictorApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'INTD',
        ])->sum('paid_value');
        $interdictorUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'INTD',
        ])->sum('loss_value');
        $interdictorDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'INTD',
        ])->sum('loss_value');

        $t1bsApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'T1BS',
        ])->sum('paid_value');
        $t1bsUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'T1BS',
        ])->sum('loss_value');
        $t1bsDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'T1BS',
        ])->sum('loss_value');

        $dksApproved = SRPShip::where([
            'approved' => 'Approved',
            'ship_type' => 'DKS',
        ])->sum('paid_value');
        $dksUnderReview = SRPShip::where([
            'approved' => 'Under Review',
            'ship_type' => 'DKS',
        ])->sum('loss_value');
        $dksDenied = SRPShip::where([
            'approved' => 'Denied',
            'ship_type' => 'DKS',
        ])->sum('loss_value');


        //Add string column, number columns.
        $costCodeChart->addStringColumn('SRP Costs')
                      ->addNumberColumn('Approved')
                      ->addNumberColumn('Under Review')
                      ->addNumberColumn('Denied')
                      ->addRow(['T1FDC', $t1fdcApproved, $t1fdcUnderReview, $t1fdcDenied])
                      ->addRow(['T1BC', $t1bcApproved, $t1bcUnderReview, $t1bcDenied])
                      ->addRow(['T1BS', $t1bsApproved, $t1bsUnderReview, $t1bsDenied])
                      ->addRow(['T2FD', $t2fdApproved, $t2fdUnderReview, $t2fdDenied])
                      ->addRow(['T2C', $t2cApproved, $t2cUnderReview, $t2cDenied])
                      ->addRow(['T1T2Logi', $t1t2logiApproved, $t1t2logiUnderReview, $t1t2logiDenied])
                      ->addRow(['T3D', $t3dApproved, $t3dUnderReview, $t3dDenied])
                      ->addRow(['T3C', $t3cApproved, $t3cUnderReview, $t3cDenied])
                      ->addRow(['RECON', $reconsApproved, $reconsUnderReview, $reconsDenied])
                      ->addRow(['COMMAND', $commandApproved, $commandUnderReview, $commandDenied])
                      ->addRow(['DKS', $dksApproved, $dksUnderReview, $dksDenied]);
        
        $lava->ColumnChart('Cost Codes', $costCodeChart, [
            'columns' => 4,
            'title' => 'Cost Code SRP Chart',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
            ],
        ]);  

        return view('srp.admin.statistics')->with('lava', $lava);
    }

    public function displayCostCodes() {
        //Declare some variables
        $costcodes = array();
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
                //Store the data in a temporary variable
                $block = [
                    'code' => $tempCode,
                    'description' => $tempDescription,
                    'payout' => $tempPayout,
                ];

                //Push the data into the array
                array_push($costcodes, $block);
            }            
        }

        return view('srp.admin.costcodes.display')->with('costcodes', $costcodes);
    }

    public function displayAddCostCode() {
        return view('srp.admin.costcodes.add');
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

            return redirect('/srp/admin/display')->with('success', 'Cost code added.');
        } else {
            return redirect('/srp/admin/display')->with('error', 'Cost code already exists in the database.');
        }
    }

    public function modifyCostCodes(Request $request) {
        $this->validate($request, [
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
