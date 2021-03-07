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
use App\Library\Helpers\LookupHelper;
use App\Library\Helpers\StructureHelper;
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
     * Display the users invoices
     */
    public function DisplayInvoices() {
        //Declare variables
        $paidAmount = 0.00;
        $unpaidAmount = 0.00;

        //Get the unpaid invoices
        $unpaid = Invoice::where([
            'status' => 'Pending',
            'character_id' => auth()->user()->getId(),
        ])->paginate(15);

        //Get the late invoices
        $late = Invoice::where([
            'status' => 'Late',
            'character_id' => auth()->user()->getId(),
        ])->paginate(10);
        
        //Get the deferred invoices
        $deferred = Invoice::where([
            'status' => 'Deferred',
            'character_id' => auth()->user()->getId(),
        ])->paginate(10);

        //Get the paid invoices
        $paid = Invoice::where([
            'status' => 'Paid',
            'character_id' => auth()->user()->getId(),
        ])->paginate(15);

        //Total up the unpaid invoices
        foreach($unpaid as $un) {
            $unpaidAmount += $un->amount;
        }

        //Total up the paid invoices
        foreach($paid as $p) {
            $paidAmount += $p;
        }

        return view('miningtax.user.display.invoices')->with('unpaid', $unpaid)
                                                      ->with('late', $late)
                                                      ->with('deferred', $deferred)
                                                      ->with('paid', $paid)
                                                      ->with('unpaidAmount', $unpaidAmount)
                                                      ->with('paidAmount', $paidAmount);
    }

    /**
     * Display all of the upcoming extractions
     */
    public function DisplayUpcomingExtractions() {
        
        //Declare variables
        $structures = array();
        $sHelper = new StructureHelper;
        $esiHelper = new Esi;
        $config = config('esi');

        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the correct scopes for the services site.');
        }

        //Get the esi data for extractions
        try {
            $extractions = $esi->invoke('get', '/corporation/{corporation_id}/mining/extractions/', [
                'corporation_id' => $config['corporation'],
            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Could not retrieve extractions from ESI in MiningTaxesController.php');
            $extractions = null;
        }

        //Basically get the structure info and attach it to the variable set
        foreach($extractions as $ex) {
            $sName = $sHelper->GetStructureInfo($ex->structure_id);
            array_push($structures, [
                'structure_name' => $sName,
                'start_time' => $ex->extraction_start_time,
                'arrival_time' => $ex->chunk_arrival_time,
                'decay_time' => $ex->natural_decay_time,
            ]);
        }

        //Return the view with the extractions variable for html processing
        return view('miningtax.user.display.upcoming')->with('extractions', $extractions);
    }

    /**
     * Display a calendar of upcoming extractions
     */
    public function DisplayExtractionCalendar() {
        //Declare variables
        $structures = array();
        $sHelper = new StructureHelper;
        $lava = new Lavacharts;
        $esiHelper = new Esi;
        $config = config('esi');

        //Check for the correct scopes
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the correct scopes for the services site.');
        }

        //Get the esi data for extractions
        try {
            $response = $esi->invoke('get', '/corporation/{corporation_id}/mining/extractions', [
                'corporation_id' => $config['corporation'],
            ]);
        } catch(RequestFailedException $e) {
            Log::critical('Could not retrieve the extractions from ESI in DisplayExtractionCalendar in MiningTaxesController');
            return redirect('/dashboard')->with('error', 'Failed to get extraction data from ESI');
        }

        //Decode the extraction data from ESI
        $extractions = json_decode($response, false);

        /**
         * Create a 3 month calendar for the past, current, and future extractions
         */
        //Create the data tables
        $calendar = $lava->DataTable();
        
        $calendar->addDateTimeColumn('Date')
                 ->addBooleanColumn('YesNo')
                 ->addStringColumn('Extractions');

        foreach($extractions as $extract) {
            $calendar->addRow([
                $extract->chunk_arrival_time,
                1
            ]);
        }    
                
        $lava->CalendarChart('Extractions', $calendar, [
            'title' => 'Upcoming Extractions',
            'unusedMonthOutlineColor' => [
                'stroke' => '#ECECEC',
                'strokeOpacity' => 0.75,
                'strokeWidth' => 1,
            ],
            'dayOfWeekLabel' => [
                'color' => '#4f5b0d',
                'fontSize' => 16,
                'italic' => true,
            ],
            'noDataPattern' => [
                'color' => '#DDD',
                'backgroundColor' => '#11FFFF',
            ],
            'colorAxis' => [
                'values' => [0, 100],
                'colors' => ['black', 'green'],
            ],
        ]);

        return view('miningtax.user.display.calendar')->with('calendar', $calendar)
                                                      ->with('lava', $lava);
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

                    array_push($structures, [
                        'name' => $structure->name,
                    ]);
                }
            }
        }

        //Return the view
        return view('miningtax.user.display.ledger')->with('miningLedgers', $miningLedgers)
                                                    ->with('structures', $structures);
    }
}
