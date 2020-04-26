<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Carbon\Carbon;

//App Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Structures\StructureHelper;
use App\Library\Lookups\LookupHelper;

//App Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Structure\Structure;
use App\Models\Structure\Service;
use App\Models\Lookups\ItemLookup;
use App\Models\MoonRent\MoonRental;
use App\Models\Moon\Moon;

class MoonLedgerController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:corp.lead');
    }

    public function displayRentalMoon() {
        
    }

    public function displayMoonLedger() {
        //Declare variables
        $structures = array();
        $miningLedgers = array();
        $tempMiningLedger = array();
        $tempMining = array();
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $response = null;
        $structureInfo = null;

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope(auth()->user()->getId(), 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Need to add scopes for esi-industry.read_corporation_mining.v1');
        } else {
            if(!$esiHelper->HaveEsiScope(auth()->user()->getId(), 'esi-universe.read_structures.v1')) {
                return redirect('/dashboard')->with('error', 'Need to add scope for esi-universe.read_structures.v1');
            }
        }

        //Get the refresh token if scope checks have passed
        $refreshToken = $esiHelper->GetRefreshToken(auth()->user()->getId());
        
        //Setup the esi container
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo(auth()->user()->getId());

        //Try to get the mining observers for the corporation from esi
        try {
            $responses = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
                'corporation_id' => $character->corporation_id,
            ]);
        } catch(RequestFailedException $e) {
            //If an exception has occurred for some reason redirect back to the dashboard with an error message
            return redirect('/dashboard')->with('error', 'Failed to get mining structures.');
        }

        //For each mining observer, let's build the array of data to show on the page
        foreach($responses as $response) {
            //Try to get the structure information from esi
            try {
                $structureInfo = $esi->invoke('get', '/universe/structures/{structure_id}/', [
                    'structure_id' => $response->observer_id,
                ]);
            } catch(RequestFailedException $e) {
                //If an exception has occurred, then do nothing
            }

            //We don't really care about the key, but it is better than just 0 through whatever number
            $structures[$response->observer_id] = $structureInfo->name;
        }

        //For each of the structures we want to address it by it's key value pair.
        //This will allow us to do some interesting things in the display.
        foreach($structures as $key => $value) {
            try {
                $ledgers = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                    'corporation_id' => $character->corporation_id,
                    'observer_id' => $key,
                ]);
            } catch(RequestFailedException $e) {
                $ledgers = null;
            }

            if($ledgers != null) {
                foreach($ledgers as $ledger) {
                    //Declare a variable that will need to be cleared each time the foreach processes
                    $tempArray = array();

                    //Get the character information from the character id
                    $charInfo = $lookup->GetCharacterInfo($ledger->character_id);
                    //Get the corp ticker
                    $corpInfo = $lookup->GetCorporationInfo($charInfo->corporation_id);
                    //Get the ore name from the type id
                    $ore = $lookup->ItemIdToName($ledger->type_id);

                    //We only want to push the mining ledger entry into the array if it matches
                    //the date within 30 days
                    $sortTime = Carbon::now()->subDays(30);
                    $current = Carbon::createFromFormat('Y-m-d', $ledger->last_updated);
                    if($current->greaterThanOrEqualTo($sortTime)) {
                        array_push($tempMiningLedger, [
                            'structure' => $value,
                            'character' => $charInfo->name,
                            'corpTicker' => $corpInfo->ticker,
                            'ore' => $ore,
                            'quantity' => $ledger->quantity,
                            'updated' => $ledger->last_updated,
                        ]);
                    }
                }
                
                //Store the ledger permanently by structure name
                array_push($miningLedgers, $tempMiningLedger);
            }
        }

        dd($miningLedgers);

        return view('moons.ledger.corpmoons')->with('miningLedgers', $miningLedgers)
                                             ->with('structures', $structures);
    }

    public function displaySelection() {
        //Declare variables
        $structures = array();
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $response = null;
        $structureInfo = null;

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope(auth()->user()->getId(), 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Need to add scopes for esi-industry.read_corporation_mining.v1');
        } else {
            if(!$esiHelper->HaveEsiScope(auth()->user()->getId(), 'esi-universe.read_structures.v1')) {
                return redirect('/dashboard')->with('error', 'Need to add scope for esi-universe.read_structures.v1');
            }
        }
        
        //Get the refresh token if scope checks have passed
        $refreshToken = $esiHelper->GetRefreshToken(auth()->user()->getId());
        
        //Setup the esi container
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo(auth()->user()->getId());
        
        //Try to get the mining observers for the corporation from esi
        try {
            $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/', [
                'corporation_id' => $character->corporation_id,
            ]);
        } catch(RequestFailedException $e) {
            //If an exception has occurred for some reason redirect back to the dashboard with an error message
            return redirect('/dashboard')->with('error', 'Failed to get mining structures.');
        }

        foreach($response as $resp) {
            //Try to get the structure information from esi
            try {
                $structureInfo = $esi->invoke('get', '/universe/structures/{structure_id}/', [
                    'structure_id' => $resp->observer_id,
                ]);
            } catch(RequestFailedException $e) {
                //If an exception has occurred, then do nothing
            }

            $structures[$resp->observer_id] = $structureInfo->name;
        }

        return view('moons.ledger.displayselect')->with('structures', $structures);
    }

    public function displayLedger(Request $request) {
        //Declare variables
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $tempMining = array();
        $mining = array();
        $temp = array();
        $ledgerEntries = array();

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope(auth()->user()->getId(), 'esi-industry.read_corporation_mining.v1')) {
            //If the scope check fails, return with a redirect and error message
            return redirect('/dashboar')->with('error', 'Could not find the scope for esi-industry.read_corporation_mining.v1');
        }

        $refreshToken = $esiHelper->GetRefreshToken(auth()->user()->getId());
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo(auth()->user()->getId());

        //Try to get the mining ledger for the corporation observer
        try {
            $ledgers = $esi->invoke('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', [
                'corporation_id' => $character->corporation_id,
                'observer_id' => $request->structure,
            ]);
        } catch(RequestFailedException $e) {
            return redirect('/dashboard')->with('error', 'Failed to get the mining ledger.');
        }

        //Cycle through each ledger entry and create a new array
        foreach($ledgers as $ledger) {
            //Get the character information from the character id
            $charInfo = $lookup->GetCharacterInfo($ledger->character_id);
            //Get the corp ticker
            $corpInfo = $lookup->GetCorporationInfo($charInfo->corporation_id);
            //Get the ore name from the type id
            $ore = $lookup->ItemIdToName($ledger->type_id);

            //Push the data onto the mining arary
            array_push($tempMining, [
                'character' => $charInfo->name,
                'corpTicker' => $corpInfo->ticker,
                'ore' => $ore,
                'quantity' => $ledger->quantity,
                'updated' => $ledger->last_updated,
            ]);
        }

        //Sort through the array and reverse it so the last day is shown first
        $tempMining = array_reverse($tempMining, true);

        //Create the current time based on the current time and then subtract 30 days
        $sortTime = Carbon::now()->subDays(30);

        //Sort through the array and discard anything that is further than 30 days out.
        foreach($tempMining as $entry) {
            //Create the current time stamp from the current legder before sorting
            $current = Carbon::createFromFormat('Y-m-d', $entry['updated']);
            if($current->greaterThanOrEqualTo($sortTime)) {
                array_push($mining, $entry);
            }
        }

        //Return the view with the data
        return view('moons.ledger.displayledger')->with('mining', $mining);
    }
}
