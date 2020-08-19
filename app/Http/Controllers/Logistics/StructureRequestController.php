<?php

namespace App\Http\Controllers\Logistics;

//Internal Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Carbon\Carbon;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

//Library Helpers
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\Logistics\AnchorStructure;
use App\Models\User\UserPermission;

class StructureRequestController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
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

        $config = config('esi');

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

        //Send a mail out to the FC Team
        $fcTeam = UserPermission::where([
            'permission' => 'fc.team',
        ])->get();

        //Set the mail delay
        $delay = 30;

        foreach($fcTeam as $fc) {
            $body = "Structure Anchor Request has been entered.<br>";
            $body .= "Please check the W4RP Services Site for the structure information.<br>";
            $body .= "<br>Sincerely,<br>";
            $body .= "Warped Intentions Leadership<br>";
            
            //Dispatch the mail job
            $subject = "New Structure Anchor Request";
            ProcessSendEveMailJob::dispatch($body, (int)$fc->character_id, 'character', $subject, $config['primary'])->onQueue('mail')->delay(Carbon::now()->addSeconds($delay));

            $delay += 30;
        }

        return redirect('/dashboard')->with('success', 'Structure request successfully submitted.');
    }
}
