<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Library\Fleet;
use App\Library\Esi;


class FleetsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayRegisterFleet() {
        return view('fleets.registerfleet');
    }

    public function displayFleets() {
        $fleets = \App\Models\Fleet::all();
        $data = array();
        $fc = array();
        $fleet = array();
        $description = array();
        $i = 0;

        foreach($fleets as $fl) {
            $fc[$i] = $fl->character_id;
            $fleet[$i] = $fl->fleet;
            $description[$i] = $fl->description;
            $i++;
        }

        $data = [
            $fc,
            $fleet,
            $description,
        ];

        $size = sizeof($fc);

        //Return the view with the array of the fleet
        return view('fleets.displayfleets')->with('data', $data);
    }

    public function registerFleet(Request $request) {
        //Register a new instance of the fleet class
        $fleet = new Fleet(Auth::user()->character_id);
        $esiHelper = new Esi();
        if(!$esiHelper->HaveEsiScope(Auth::user()->character_id, 'esi-fleets.write_fleet.v1')) {
            return view('inc.error')->with('error', 'User does not have the write fleet scope.');
        }
        
        //Make the fleet uri so we can call later functions
        $fleetUri = $fleet->SetFleetUri($request->fleetUri);
        
        //Check for the fleet in the database
        $check = DB::table('Fleets')->where('fleet', $fleetUri)->first();
        //If we do not find the fleet, let's create it.
        if($check === null) {
               
            $current = Carbon::now();
            //If we are between 00:00 and 05:00, we want to set the end time for 0500
            if($current->hour > 0 && $current->hour < 5) {
                //Figure out the hours to add to the fleet before purging it.
                $hour = $current->hour;
                $endTime = Carbon::now();
                $endTime->hour = 11 - $hour;
            } else {
                //Figure out the hours to add to the fleet before purging it.
                $endTime = Carbon::now();
                $endTime->day++;
                $endTime->hour = 11;
                $endTime->minute = 0;
                $endTime->second = 0;
            }
            // Insert the fleet into the table
            DB::table('Fleets')->insert([
                'character_id' => Auth::user()->character_id,
                'fleet' => $fleetUri,
                'description' => $request->description,
                'creation_time' => $current,
                'fleet_end' => $endTime,
            ]);
            
            $fleet->SetFleetEndTime($endTime);
        } 

        return redirect('/fleets/display');
    }

    public function deleteFleet($fleetId) {
        DB::table('Fleets')->where('fleet', $fleetId)->delete();
        return redirect('/fleets/display');
    }

    public function addPilot($fleetId, $charId) {
        $newPilot = new Fleet();

        //Retrieve the fleet data
        $fleet = DB::table('Fleets')->where('fleet', $fleetId)->get();
        /**
         * Add the pilot to the fleet
         * @var fleet[0]->character_id is the FC of the fleet to get the refresh token
         * @var charId is the character being added to the fleet
         * @var fleetId is the number of the fleet to be retrieved from the database
         */
        $error = $newPilot->AddPilot($fleet[0]->character_id, $charId, $fleetId);
        //If we don't have an error go back to the dashboard, 
        //Otherwise, send the user to the error screen and print the error out.
        if($error === null) {
            return view('/dashboard')->with('success', 'Invite for fleet sent.');
        } else {
            return view('inc.error')->with('error', $error);
        }
    }

    public function addPilotName($fleetId, $name) {
        $newPilot = new Fleet();
        $esiHelper = new Esi();

        //Retrieve the fleet data
        $fleet = DB::table('Fleets')->where('fleet', $fleetId)->get();
        //Search for the pilot's character id through his name
        $charId = $esiHelper->FindCharacterId($name);
        /**
         * Add the pilot to the fleet
         * @var fleet[0]->character_id is the FC of the fleet to get the refresh token
         * @var charId is the character being added to the fleet
         * @var fleetId is the number of the fleet to be retrieved from the database
         */
        $error = $newPilot->AddPilot($fleet[0]->character_id, $charId, $fleetId);
        //If we don't have an error go back to the dashboard, 
        //Otherwise, send the user to the error screen and print the error out.
        if($error === null) {
            return view('/dashboard')->with('success', 'Invite for fleet sent.');
        } else {
            return view('inc.error')->with('error', $error);
        }
    }

    public function updateFleet() {
        //Retrieve the fleet from the session
        $fleet = session('fleet');
        $fleet->UpdateFleet($request->isFreeMove, $request->motd);

        return redirect('/fleets/display');
    }
}
