<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;
//Collection Stuff
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
use App\Models\User\User;

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
        $esiHelper = new Esi;
        $config = config('esi');
        $sHelper = new StructureHelper($config['primary'], $config['corporation']);
        $structures = array();
        $structuresCalendar = array();
        $lava = new Lavacharts;

        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the correct scopes for the services site.');
        }

        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Get the esi data for extractions
        try {
            $extractions = $esi->invoke('get', '/corporation/{corporation_id}/mining/extractions/', [
                'corporation_id' => $config['corporation'],
            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Could not retrieve extractions from ESI in MiningTaxesController.php');
            return redirect('/dashboard')->with('error', "Could not pull extractions from ESI data.");
        }

        //Basically get the structure info and attach it to the variable set
        foreach($extractions as $ex) {
            $sName = $sHelper->GetStructureInfo($ex->structure_id);
            array_push($structures, [
                'structure_name' => $sName->name,
                'start_time' => $esiHelper->DecodeDate($ex->extraction_start_time),
                'arrival_time' => $esiHelper->DecodeDate($ex->chunk_arrival_time),
                'decay_time' => $esiHelper->DecodeDate($ex->natural_decay_time),
            ]);
        }

        //Sort extractions by arrival time
        $structuresCollection = collect($structures);
        $sorted = $structuresCollection->sortBy('arrival_time');
        //Store the sorted collection back into the variable before being used again.
        $structures = $sorted->all();

        /**
         * Create a 3 month calendar for the past, current, and future extractions
         */
        //Create the data tables
        $calendar = $lava->DataTable();
        
        $calendar->addDateTimeColumn('Date')
                 ->addNumberColumn('Total');

        foreach($extractions as $extraction) {
            array_push($structuresCalendar, [
                'date' => $esiHelper->DecodeDate($extraction->chunk_arrival_time),
                'total' => 0,
            ]);
        }

        foreach($extractions as $extraction) {
            for($i = 0; $i < sizeof($structuresCalendar); $i++) {
                //Create the dates in a carbon object, then only get the Y-m-d to compare.
                $tempStructureDate = Carbon::createFromFormat('Y-m-d H:i:s', $structuresCalendar[$i]['date'])->toDateString();
                $extractionDate = Carbon::createFromFormat('Y-m-d H:i:s', $esiHelper->DecodeDate($extraction->chunk_arrival_time))->toDateString();
                //check if the dates are equal then increase the total by 1
                if($tempStructureDate == $extractionDate) {
                    $structuresCalendar[$i]['total'] += 1;
                }
            }
        }

        foreach($structuresCalendar as $structureC) {
            $calendar->addRow([
                $structureC['date'],
                $structureC['total'],
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
                'values' => [0, 5],
                'colors' => ['green', 'red'],
            ],
        ]);

        //Return the view with the extractions variable for html processing
        return view('miningtax.user.display.upcoming')->with('structures', $structures)
                                                      ->with('lava', $lava)
                                                      ->with('calendar', $calendar);
    }

    /**
     * Display the ledger for the moons.
     */
    public function DisplayMoonLedgers() {
        //Declare variables
        $structures = array();
        $tempLedgers = array();
        $miningLedgers = array();
        $ledgers = array();
        $esiHelper = new Esi;
        $lookup = new LookupHelper;
        $config = config('esi');

        //Check for the esi scope
        if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-industry.read_corporation_mining.v1')) {
            return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the ESI for the holding corp for corp mining.');
        } else {
            if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-universe.read_structures.v1')) {
                return redirect('/dashboard')->with('error', 'Tell the nub Minerva to register the ESI for the holding corp for structures.');
            }
        }

        //Get the refresh token if scope checks have passed
        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        
        //Setup the esi container
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);
        //Declare the structure helper after the esi container has been created
        $sHelper = new StructureHelper($config['primary'], $config['corporation'], $esi);

        //Get the character data from the lookup table if possible or esi
        $character = $lookup->GetCharacterInfo($config['primary']);

        //Get the observers from the database
        $observers = Observer::all();

        $corpInfo = $lookup->GetCorporationInfo(auth()->user()->getId());
        dd($corpInfo);

        //Get the ledgers for each structure one at a time
        foreach($observers as $obs) {
            //Get the structure information
            $structureInfo = $sHelper->GetStructureInfo($obs->observer_id);

            //Add the name to the structures array
            array_push($structures, [
                'name' => $structureInfo->name,
            ]);
            /**
             * Get the ledger from each observer.
             * We don't care about observer type as it can only be an Athanor or Tatara
             */
            $ledgers = Ledger::where([
                'observer_id' => $obs->observer_id,
                'character_id' => auth()->user()->getId(),
            ])->where('last_updated', '>=', Carbon::now()->subDays(30))->get();

            if($ledgers->count() > 0) {
                foreach($ledgers as $ledger) {
                    //Foreach ledger add it to the array
                    array_push($miningLedgers, [
                        'structure' => $structureInfo->name,
                        'character' => auth()->user()->getName(),
                        'corpTicker' => $corpInfo->ticker,
                        'ore' => $ore,
                        'quantity' => $ledger->quantity,
                        'updated' => $ledger->last_updated,
                    ]);
                }
            }
        }

        //Return the view
        return view('miningtax.user.display.ledger')->with('miningLedgers', $miningLedgers)
                                                    ->with('structures', $structures);
    }
}
