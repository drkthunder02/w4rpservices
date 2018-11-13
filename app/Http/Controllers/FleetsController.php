<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Library\Fleet;


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
        $fleets = DB::table('Fleets')->get();
        //Return the view with the array of the fleet
        return view('fleets.displayfleets')->with('fleetId', $fleetId);
    }

    public function registerFleet(Request $request) {
        $fleet = new Fleet(Auth::user()->character_id);
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
                $endTime->hour = 5 - $hour;
            } else {
                //Figure out the hours to add to the fleet before purging it.
                $endTime = Carbon::now();
                $endTime->day++;
                $endTime->hour = 5;
                $endTime->minute = 0;
                $endTime->second = 0;
            }
            // Insert the fleet into the table
            DB::table('Fleets')->insert([
                'character_id' => Auth::user()->character_id,
                'fleet' => $fleetUri,
                'creation_time' => $current,
                'fleet_end' => $endTime,
            ]);
            
            $fleet->SetFleetEndTime($endTime);
            //Set the fleet into the session to be used later
            session(['fleet' => $fleet]);

            //Return the view with the success message
            return view('fleets.displayfleets')->with('success', 'Fleet registered.');
        } else {
            //Return the view with the error message of the fleet has been found already.
            return view('fleets.displayfleets')->with('error', 'Fleet already in the database.');
        }
    }

    public function addPilot($id) {
        //Retrieve the fleet from the session
        $fleet = session('fleet');
        dd($fleet);
        //Add a pilot to the fleet
        $error = $fleet->AddPilot($id);
        if($error) {
            return view('fleets.displaystanding')->with('error', 'Unable to add to fleet.');
        } else {
            return view('fleets.displaystanding')->with('success', 'Pilot added to fleet.');
        }
    }

    public function updateFleet() {
        //Retrieve the fleet from the session
        $fleet = session('fleet');
        $fleet->UpdateFleet($request->isFreeMove, $request->motd);

        return view('fleets.displaystanding')->with('success', 'Fleet updated.');
    }
}
