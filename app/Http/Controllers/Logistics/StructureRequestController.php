<?php

namespace App\Http\Controllers\Logistics;

//Internal Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Log;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Library Helpers
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\Logistics\AnchorStructure;
use App\Models\Mail\SentMail;
use App\Models\Jobs\JobSendEveMail;

class StructureRequestController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function displayForm() {
        return view('structurerequest.requeststructure');
    }

    public function storeForm(Request $request) {
        $this->validate($request, [
            'corporation_name' => 'required',
            'system' => 'required',
            'structure_size' => 'required',
            'structure_type' => 'required',
            'requested_drop_time' => 'required',
            'requester' => 'required',
        ]);

        $lookup = new LookupHelper;

        $requesterId = $lookup->CharacterNameToId($request->requester);
        $corporationId = $lookup->CorporationNameToId($request->corporation_name);
        
        AnchorStructure::insert([
            'corporation_id' => $corporationId,
            'corporation_name' => $request->corporation_name,
            'system' => $request->system,
            'structure_size' => $request->structure_size,
            'structure_type' => $request->structure_type,
            'requested_drop_time' => $request->requested_drop_time,
            'requester_id' => $requesterId,
            'requester' => $request->requester,
        ]);

        return redirect('/structures/display/requests');
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
