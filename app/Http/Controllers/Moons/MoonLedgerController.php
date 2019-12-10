<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use DB;

//App Library
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Structures\StructureHelper;
use App\Library\Lookups\NewLookupHelper;

//App Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Structure\Structure;
use App\Models\Structure\Service;
use App\Models\Lookups\Item;

class MoonLedgerController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:corp.lead');
    }

    public function displaySelection() {
        //Declare variables
        $structures = array();
        $esiHelper = new Esi;
        $lookup = new NewLookupHelper;
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
        $lookup = new NewLookupHelper;
        $mining = array();

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

        foreach($ledgers as $ledger) {
            $char = $lookup->CharacterIdToName($ledger->character_id);
            $ore = $lookup->ItemIdToName($ledger->type_id);

            $temp = [
                'character' => $char,
                'ore' => $ore,
                'quantity' => $quantity,
            ];

            array_push($mining, $temp);
        }

        return view('moons.ledger.displayledger')->with('mining', $mining);
    }
}
