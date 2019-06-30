<?php

namespace App\Http\Controllers\SRP;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

//User Libraries

//Models
use App\Models\SRP\Ship;

class SRPAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:srp.admin');
    }

    public function displaySRPRequests() {
        $this->middleware('permission:srp.admin');

        $requests = Ship::where(['approved' => 'Under Review'])->get();

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
