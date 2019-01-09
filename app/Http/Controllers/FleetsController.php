<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Library\Fleets\FleetHelper;
use App\Library\Esi\Esi;

use App\Models\Fleet\Fleet;
use App\Models\Fleet\FleetActivity;


class FleetsController extends Controller
{
    /**
     * Construction function
     * 
     * returns nothing
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:role.user');
    }
    
    /**
     * Records fleet activity when requested from the fleet info page
     * 
     * @var fleetId
     */
    public function storeFleetActivity($fleetId) {
        $fleet = new FleetHelper();
        $fleet->StoreFleetActivity($fleetId);

        return redirect('/display/fleets')->with('success', 'Fleet activity recorded.');
    }

    /**
     * Displays the blade for registering a fleet
     * 
     * @var (none) 
     */
    public function displayRegisterFleet() {
        return view('fleets.registerfleet');
    }

    /**
     * Work in progress.  Doesn't do anything yet.
     * 
     * @var (none)
     */
    public function displayFleetSetup() {

        return 0;
    }

    /**
     * Displays all currently recorded fleets
     * 
     * @var (none)
     */
    public function displayFleets() {
        $fleets = Fleet::all();
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

        //Return the view with the array of the fleet
        return view('fleets.displayfleets')->with('data', $data);
    }

    /**
     * Allows a person to register a fleet through services.
     * 
     * @var Request
     */
    public function registerFleet(Request $request) {
        //Register a new instance of the fleet class
        $fleet = new FleetHelper(Auth::user()->character_id, $request->fleetUri);
        $esiHelper = new Esi();
        if(!$esiHelper->HaveEsiScope(Auth::user()->character_id, 'esi-fleets.write_fleet.v1')) {
            return view('inc.error')->with('error', 'User does not have the write fleet scope.');
        }
        
        //Make the fleet uri so we can call later functions
        $fleetUri = $fleet->GetFleetUri();
        
        //Check for the fleet in the database
        $check = DB::table('Fleets')->where('fleet', $fleetUri)->first();
        //If we do not find the fleet, let's create it.
        if($check === null) {
            // Insert the fleet into the table
            DB::table('Fleets')->insert([
                'character_id' => Auth::user()->character_id,
                'fleet' => $fleetUri,
                'description' => $request->description,
                'creation_time' => $current,
            ]);
        } 

        return redirect('/fleets/display');
    }

    /**
     * Deletes a fleet of fleetId
     * 
     * @var fleetId
     */
    public function deleteFleet($fleetId) {
        DB::table('Fleets')->where('fleet', $fleetId)->delete();
        return redirect('/fleets/display');
    }

    /**
     * Add a pilot to the fleet
     * 
     * @var fleetId
     * @var charId
     */
    public function addPilot($fleetId, $charId) {
        $newPilot = new FleetHelper();

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

    /**
     * Add a pilot by his name rather than allowing him to click on the link
     * 
     * @var fleetId
     * @var name
     */
    public function addPilotName($fleetId, $name) {
        $newPilot = new FleetHelper();
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

    /**
     * Update a fleet based on a session variable
     * 
     * @var session
     */
    public function updateFleet() {
        //Retrieve the fleet from the session
        $fleet = session('fleet');
        $fleet->UpdateFleet($request->isFreeMove, $request->motd);

        return redirect('/fleets/display');
    }
}
