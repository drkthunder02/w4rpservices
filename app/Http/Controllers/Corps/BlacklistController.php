<?php

namespace App\Http\Controllers\Corps;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

//Library
use App\Library\Lookups\NewLookupHelper;

//Models
use App\Models\Character\BlacklistUser;

class BlacklistController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function AddToBlacklist(Request $request) {
        //Middleware needed for the function
        $this->middleware('permission:alliance.recruiter');

        //Validate the user input
        $this->validate($request, [
            'name' => 'required',
            'reason' => 'required',
        ]);

        //Create the library variable
        $lookup = new NewLookupHelper;

        //See if the character is already on the list
        $count = BlacklistUser::where([
            'name' => $request->name,
        ])->count();

        //If the count is 0, then add the character to the blacklist
        if($count === 0) {
            //Get the character id from the universe end point
            $charId = $lookup->CharacterNameToId($request->name);

            //Insert the character into the blacklist table
            BlacklistUser::insert([
                'character_id' => $charId,
                'name' => $request->name,
                'reason' => $request->reason,
            ]);
        } else {
            //Return the view
            return view('blacklist.add')->with('error', 'Character is already on the black list.');
        }

        //Return the view
        return view('blacklist.list')->with('success', 'Character added to the blacklist');
    }

    public function RemoveFromBlacklist(Request $request) {
        //Middleware needed
        $this->middleware('permission:alliance.recruiter');

        //Validate the input request
        $this->validate($request, [
            'name' => 'required',
        ]);

        //Delete the blacklist character
        BlacklistUser::where([
            'name' => $request->name,
        ])->delete();

        //Return the view
        return view('blacklist.list')->with('success', 'Character removed from the blacklist.');
    }

    public function DisplayBlacklist() {
        //Middleware needed
        $this->middleware('permission:corp.recruiter');

        //Get the entire blacklist
        $blacklist = BlacklistUser::all();

        //Return the view with the data
        return view('blacklist.list')->with('blacklist', $blacklist);
    }

    public function SearchInBlacklist(Request $request) {
        //Middleware needed
        $this->middleware('permission:corp.recruiter');

        //Validate the input from the form
        $this->validate($request, [
            'name' => 'required',
        ]);

        //Get the data being requested
        $blacklistCount = BlacklistUser::where([
            'name' => $request->name,
        ])->count();

        //If the count for the blacklist is greater than 0, then  get the details, and send it to the view
        if($blacklistCount > 0) {
            //Try to find the user in the blacklist
            $blacklist = BlacklistUser::where([
                'name' => $request->name,
            ])->first();

            //Send the data to the view
            return view('blacklist.list')->with('blacklist', $blacklist)
                                         ->with('success', 'Name was found on the blacklist');
        } else {
            //If they aren't found, then null out the blacklist variable, and send to the view
            $blacklist = null;

            return view('blacklist.list')->with('blacklist', $blacklist)
                                         ->with('error', 'Name was not found on the blacklist.');
        }
    }
}
