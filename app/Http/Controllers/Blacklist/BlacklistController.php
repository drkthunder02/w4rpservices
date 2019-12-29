<?php

namespace App\Http\Controllers\Blacklist;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use DB;

//Library
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\Blacklist\BlacklistEntity;
use App\Models\User\User;
use App\Models\User\UserRole;
use App\Models\User\UserPermission;

class BlacklistController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function DisplayAddToBlacklist() {
        return view('blacklist.add');
    }

    public function DisplayRemoveFromBlacklist() {
        return view('blacklist.remove');
    }

    public function DisplaySearch() {
        return view('blacklist.search');
    }

    public function AddToBlacklist(Request $request) {
        //Middleware needed for the function
        $this->middleware('permission:alliance.recruiter');

        //Validate the user input
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'reason' => 'required',
        ]);

        //Create the library variable
        $lookup = new LookupHelper;
        //Declare other necessary variables
        $charId = null;
        $corporationId = null;
        $allianceId = null;
        $entityId = null;
        $entityType = null;

        //See if the entity is already on the list
        $count = BlacklistEntity::where([
            'entity_name' => $request->name,
        ])->count();
        
        //If the count is 0, then add the character to the blacklist
        if($count === 0) {
            if($request->type == 'Character') {
                //Get the character id from the universe end point
                $entityId = $lookup->CharacterNameToId($request->name);
            } else if($request->type == 'Corporation') {
                //Get the corporation id from the universe end point
                $entityId = $lookup->CorporationNameToId($request->name);
            } else if($request->type == 'Alliance') {
                //Get the alliance id from the universe end point
                $entityId = $lookup->AllianceNameToId($request->name);
            } else {
                //Redirect back to the view
                return redirect('/blacklist/display/add')->with('error', 'Entity Type not allowed.');
            }

            //If all id's are null, then we couldn't find the entity
            if($entityId == null) {
                //Redirect back to the view
                return redirect('/blacklist/display/add')->with('error', 'Entity Id was not found.');
            }

            //Store the entity in the table
            BlacklistEntity::insert([
                'entity_id' => $entityId,
                'entity_name' => $request->name,
                'entity_type' => $request->type,
                'reason' => $request->reason,
                'alts' => $request->alts,
                'lister_id' => auth()->user()->getId(),
                'lister_name' => auth()->user()->getName(),
            ]);

            //Return to the view
            return redirect('/blacklist/display/add')->with('success', $request->name . ' added to the blacklist.');

        } else {
            //Return the view
            return view('blacklist.add')->with('error', 'Entity of type '. $request->entity_type . ' is already on the black list.');
        }

        //If we get back to this point redirect to the blacklist with a general error.
        return redirect('/blacklist/display/add')->with('error', 'General Error. Contact Support.');
    }

    public function RemoveFromBlacklist(Request $request) {
        //Middleware needed
        $this->middleware('permission:alliance.recruiter');

        //Validate the input request
        $this->validate($request, [
            'name' => 'required',
        ]);

        //Delete the blacklist character
        BlacklistEntity::where([
            'name' => $request->name,
        ])->delete();

        //Return the view
        return redirect('/blacklist/display')->with('success', 'Character removed from the blacklist.');
    }

    public function DisplayBlacklist() {

        //Get the entire blacklist
        $blacklist = BlacklistEntity::orderBy('entity_name', 'asc')->paginate(50);

        //Return the view with the data
        return view('blacklist.list')->with('blacklist', $blacklist);
    }

    public function SearchInBlacklist(Request $request) {

        //Validate the input from the form
        $this->validate($request, [
            'parameter' => 'required',
        ]);

        $blacklist = DB::table('alliance_blacklist')->where('entity_name', 'like', $request->parameter . "%")
                                       ->orWhere('entity_type', 'like', $request->parameter . "%")
                                       ->orWhere('alts', 'like', $request->parameter . "%")
                                       ->orWhere('reason', 'like', $request->parameter . "%")
                                       ->orderBy('entity_name', 'asc')
                                       ->paginate(50);

        $blacklistCount = sizeof($blacklist);

        //If the count for the blacklist is greater than 0, then  get the details, and send it to the view
        if($blacklistCount > 0) {

            //Send the data to the view
            return view('blacklist.list')->with('blacklist', $blacklist)
                                         ->with('success', 'Results were found on the blacklist');
        } else {
            //If they aren't found, then null out the blacklist variable, and send to the view
            $blacklist = null;

            return view('blacklist.list')->with('blacklist', $blacklist)
                                         ->with('error', 'Results were not found on the blacklist.');
        }
    }
}
