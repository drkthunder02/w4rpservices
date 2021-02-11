<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;

//Library Helpers
use App\Library\Lookups\LookupHelper;
use App\Library\Structures\StructureHelper;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\Moon\ItemComposition;
use App\Models\Moon\MineralPrice;
use App\Models\MiningTax\Ledger;
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Invoice;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

class MiningTaxesController extends Controller
{
    /**
     * Construct to deal with middleware and other items
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    /**
     * Display all of the upcoming extractions
     */
    public function DisplayUpcomingExtractions() {
        
        //Declare variables
        $structures = array();
        $sHelper = new StructureHelper;

        //Get the esi data for extractions
        try {
            $extractions = $this->esi->invoke('get', '/corporation/{corporation_id}/mining/extractions/', [
                'corporation_id' => $config['corporation'],
            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Could not retrieve extractions from ESI in MiningTaxesController.php');
            $extractions = null;
        }

        //Basically get the structure info and attach it to the variable set
        foreach($extractions as $ex) {
            $sName = $sHelper->GetStructureInfo($ex->structure_id);
            $ex->structure_name = $sName;

        }

        //Return the view with the extractions variable for html processing
        return view('miningtax.display.upcoming')->with('extractions', $extractions);
    }

    /**
     * Display the ledger for the moons.
     */
    public function DisplayMoonLedgers() {
        //Declare variables
        $structures = array();
        $tempLedgers = array();
        $ledgers = array();
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $sHelper = new StructureHelper;
        $config = config();

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the ESI for the holding corp.');
        } else {
            if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-universe.read_structures.v1')) {
                return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the ESI for the holding corp.');
            }
        }

        //Get the refresh token if scope checks have passed
        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        
        //Setup the esi container
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo($config['primary']);

        //Get the observers from the database
        $observers = Observer::all();

        //Get the ledgers for each structure one at a time
        foreach($observers as $obs) {
            /**
             * Get the ledger from each observer.
             * We don't care about observer type as it can only be an Athanor or Tatara
             */
            $ledgers = Ledger::where([
                'observer_id' => $obs->structure_id,
            ])->where('last_updated', '>=', Carbon::now()->subDays(30))->get();

            if($ledger != null) {
                foreach($ledgers as $ledger) {
                    $tempArray = array();

                    //Get the character information from the character id
                    $charInfo = $lookup->GetCharacterInfo($ledger->character_id);
                    //Get the corp ticker
                    $corpInfo = $lookup->GetCorporationInfo($charInfo->corporation_id);
                    //Get the structure name from the database
                    $structure = $sHelper->GetStructureInfo($obs->observer_id);

                    array_push($miningLedgers, [
                        'structure' => $structure->name,
                        'character' => $charInfo->name,
                        'corpTicker' => $corpInfo->ticker,
                        'ore' => $ore,
                        'quantity' => $ledger->quantity,
                        'updated' => $ledger->last_updated,
                    ]);
                }
            }
        }

        //Return the view
        return view('miningtax.display.ledger')->with('miningLedgers', $miningLedgers)
                                               ->with('structures', $structures);
    }
}
