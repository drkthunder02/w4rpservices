<?php

namespace App\Http\Controllers\Logistics;

//Internal Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

//Models
use App\Models\Logistics\AnchorStructure;
use App\Models\Jobs\JobSendEveMail;

class StructureRequestAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:fc.team');
    }

    public function displayRequests() {
        $reqs = AnchorStructure::all();

        return view('structurerequest.display.structurerequests')->with('reqs', $reqs);
    }

    public function deleteRequest(Request $request) {
        $this->validate($request, [
            'id' => 'required',
        ]);

        AnchorStructure::where([
            'id' => $request->id,
        ])->delete();

        return redirect('/structures/display/requests');
    }
}
