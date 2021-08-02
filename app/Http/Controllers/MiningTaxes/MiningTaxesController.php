<?php

namespace App\Http\Controllers\MiningTaxes;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;
use Carbon\Carbon;
use Khill\Lavacharts\Lavacharts;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

//Library Helpers
use App\Library\Helpers\LookupHelper;
use App\Library\Helpers\StructureHelper;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Moons\MoonCalc;

//Models
use App\Models\Moon\ItemComposition;
use App\Models\Moon\MineralPrice;
use App\Models\MiningTax\Ledger;
use App\Models\MiningTax\Observer;
use App\Models\MiningTax\Invoice;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\User\User;
use App\Models\MoonRental\AllianceMoon;
use App\Models\MoonRental\AllianceMoonOre;
use App\Models\MoonRental\AllianceMoonRental;

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
     * Display the moons either the person is renting, or their corp are renting
     */
    public function DisplayRentedMoons() {
        $moons = array();

        $lookup = new LookupHelper;

        $userId = auth()->user()->getId();
        $charInfo = $lookup->GetCharacterInfo(auth()->user()->getId());
        $corpId = $charInfo->corporation_id;


        $tempMoons = AllianceMoonRental::where([
            'entity_id' => $userId,
        ])->orWhere([
            'entity_id' => $corpId,
        ])->get();

        //Foreach of the moons we got let's build the moon info and the ore data
        foreach($tempMoons as $tempMoon) {
            //Get the ores for the moon
            $ores = AllianceMoonOre::where([
                'moon_id' => $tempMoon->moon_id,
            ])->get()->toArray();

            $moons->push([
                'moon_id' => $tempMoon->moon_id,
                'system' => $tempMoon->system_name,
                'moon_name' => $tempMoon->name,
                'ores' => $ores,
                'worth_amount' => $tempMoon->worth_amount,
                'rental_amount' => $tempMoon->rental_amount,
            ]);
        }

        return view('miningtax.user.display.rentedmoons')->with('moons', $moons)
                                                         ->with('ores', $ores);
    }

    /**
     * Display the page with the moon rental form
     */
    public function DisplayMoonRentalForm(Request $request) {
        $this->validate($request, [
            'moon_id' => 'required',
            'moon_name' => 'required',
            'worth_amount' => 'required',
            'rental_amount' => 'required',
        ]);

        $moon = AllianceMoon::where([
            'moon_id' => $request->moon_id,
        ])->first();

        $ores = AllianceMoonOre::where([
            'moon_id' => $request->moon_id,
        ])->get();

        return view('miningtax.user.display.moonrentals.form')->with('moon', $moon)
                                                              ->with('ores', $ores);
    }

    /**
     * Store the information from the moon rental form
     */
    public function storeMoonRentalForm(Request $request) {
        $this->validate($request, [
            'moon_id' => 'required',
            'rental_start' => 'required',
            'rental_end' => 'required',
            'entity_name' => 'required',
            'entity_type' => 'required',
        ]);
        
        $lookup = new LookupHelper;
        $entityId = null;

        //From the name and type of the entity get the entity id.
        if($request->entity_type == 'Character') {
            $entityId = $lookup->CharacterNameToId($request->entity_name);
        } else if($request->entity_type == 'Corporation') {
            $entityId = $lookup->CorporationNameToId($request->entity_name);
        } else if($request->entity_type == 'Alliance') {
            $entityId = $lookup->AllianceNameToId($request->entity_name);
        } else {
            return redirect('/dashboard')->with('error', 'Moon Rental error.  Please contact the site admin.');
        }

        //Create the next billing date from a Carbon date 3 months from the rental start
        $nextBillingDate = Carbon::create($request->rental_start)->addMonths(3);

        //Create the uniqid for the billing cycle.
        $invoiceId = "MR" . uniqid();

        //Get the moon's information from the database so we know how much to make the bill for
        $moon = AllianceMoon::where([
            'moon_id' => $request->moon_id,
        ])->first();

        //Update the data on the Alliance Moon
        AllianceMoon::where([
            'moon_id' => $request->moon_id,
        ])->update([
            'rented' => 'Yes',
        ]);

        //Insert a new moon rental into the database
        AllianceMoonRental::insert([
            'moon_id' => $moon->moon_id,
            'moon_name' => $moon->name,
            'rental_amount' => $moon->rental_amount,
            'rental_start' => $request->rental_start,
            'rental_end' => $request->rental_end,
            'next_billing_date' => $nextBillingDate,
            'entity_id' => $entityId,
            'entity_name' => $request->entity_name,
            'entity_type' => $request->entity_type,
        ]);

        return redirect('/dashboard')->with('success', 'Before placing a structure please send the ISK to the holding corp with the description of ' . $invoiceId);
    }

    public function displayAvailableMoons() {
        //Declare variables
        $moons = new Collection;
        $mHelper = new MoonCalc;
        $lookup = new LookupHelper;
        $system = array();

        /**
         * Declare our different flavors of moon goo for the blade
         */
        $r4Goo = [
            'Zeolites',
            'Sylvite',
            'Bitumens',
            'Coesite',
        ];

        $r8Goo = [
            'Cobaltite',
            'Euxenite',
            'Titanite',
            'Scheelite',
        ];

        $r16Goo = [
            'Otavite',
            'Sperrylite',
            'Vanadinite',
            'Chromite',
        ];

        $r32Goo = [
            'Carnotite',
            'Zircon',
            'Pollucite',
            'Cinnabar',
        ];  

        $r64Goo = [
            'Xenotime',
            'Monazite',
            'Loparite',
            'Ytterbite',
        ];

        //Get all of the system names from the database by plucking all the non-rented moon system names
        $systems = AllianceMoon::where([
            'rented' => 'No',
        ])->pluck('system_name')->unique()->toArray();

        //Get all of the moons which are not rented
        $allyMoons = AllianceMoon::where([
            'rented' => 'No',
        ])->get();

        //Cycle through all of the moons to create arrays of data
        foreach($allyMoons as $moon) {
            $ores = AllianceMoonOre::where([
                'moon_id' => $moon->moon_id,
            ])->get(['ore_name', 'quantity'])->toArray();
          
            if($moon->moon_type != 'R32' && $moon->moon_type != 'R64') {
                $moons->push([
                    'system' => $moon->system_name,
                    'moon_name' => $moon->name,
                    'ores' => $ores,
                    'worth_amount' => $moon->worth_amount,
                    'rental_amount' => $moon->rental_amount,
                    'moon_id' => $moon->moon_id,
                ]);
            }
        }

        return view('miningtax.user.display.moons.availablemoons')->with('moons', $moons)
                                                                  ->with('systems', $systems)
                                                                  ->with('r4Goo', $r4Goo)
                                                                  ->with('r8Goo', $r8Goo)
                                                                  ->with('r16Goo', $r16Goo)
                                                                  ->with('r32Goo', $r32Goo)
                                                                  ->with('r64Goo', $r64Goo);
    }

    /**
     * Display all the moons in Warped Intentions Sovreignty
     */
    public function displayAllMoons() {
        //Declare variables
        $moons = new Collection;
        $mHelper = new MoonCalc;
        $lookup = new LookupHelper;
        $system = array();

        /**
         * Declare our different flavors of moon goo for the blade
         */
        $r4Goo = [
            'Zeolites',
            'Sylvite',
            'Bitumens',
            'Coesite',
        ];

        $r8Goo = [
            'Cobaltite',
            'Euxenite',
            'Titanite',
            'Scheelite',
        ];

        $r16Goo = [
            'Otavite',
            'Sperrylite',
            'Vanadinite',
            'Chromite',
        ];

        $r32Goo = [
            'Carnotite',
            'Zircon',
            'Pollucite',
            'Cinnabar',
        ];  

        $r64Goo = [
            'Xenotime',
            'Monazite',
            'Loparite',
            'Ytterbite',
        ];

        $systems = [
            '0-NTIS',
            '1-NJLK',
            '35-JWD',
            '8KR9-5',
            'EIMJ-M',
            'F-M1FU',
            'G-C8QO',
            'I6M-9U',
            'L5D-ZL',
            'L-YMYU',
            'VQE-CN',
            'VR-YIQ',
            'XZ-SKZ',
            'Y-CWQY',
        ];

        //Get all of the moons which are not rented
        $allyMoons = AllianceMoon::all();

        foreach($allyMoons as $moon) {
            $ores = AllianceMoonOre::where([
                'moon_id' => $moon->moon_id,
            ])->get(['ore_name', 'quantity'])->toArray();

            $moons->push([
                'system' => $moon->system_name,
                'moon_name' => $moon->name,
                'ores' => $ores,
            ]);
        }

        return view('miningtax.user.display.moons.allmoons')->with('moons', $moons)
                                                            ->with('systems', $systems)
                                                            ->with('r4Goo', $r4Goo)
                                                            ->with('r8Goo', $r8Goo)
                                                            ->with('r16Goo', $r16Goo)
                                                            ->with('r32Goo', $r32Goo)
                                                            ->with('r64Goo', $r64Goo);
    }

    /**
     * Display an invoice based on it's id
     * 
     * @var $invoiceId
     */
    public function displayInvoice($invoiceId) {
        $ores = array();
        $moons = array();
        $totalPrice = 0.00;
        $config = config('esi');
        $structure = new StructureHelper($config['primary'], $config['corporation']);

        $systems = AllianceMoon::where([
            'rented' => 'No',
        ])->pluck('system_name')->unique()->toArray();

        //Get the invoice from the database
        $invoice = Invoice::where([
            'invoice_id' => $invoiceId,
        ])->first();

        //Get the line items for the ledger for the invoice
        $items = Ledger::where([
            'invoice_id' => $invoiceId,
        ])->get();

        //Build the total ores table for the display page
        foreach($items as $item) {
            if(!isset($ores[$item['ore_name']])) {
                $ores[$item['ore_name']] = 0;
            }
            $ores[$item['ore_name']] = $ores[$item['ore_name']] + $item['quantity'];

            $totalPrice += $item['amount'];
        }

        //Print out the lines of the ledger line by line for another table
        foreach($items as $item) {
            //Get the structure info from the database or esi
            $tempObserverInfo = $structure->GetStructureInfo($item['observer_id']);

            //Create the array for the line by line
            array_push($moons, [
                'character_name' => $item['character_name'],
                'observer_name' => $tempObserverInfo->structure_name,
                'type_id' => $item['type_id'],
                'ore_name' => $item['ore_name'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
                'tax_amount' => $item['amount'] * $config['public_mining_tax'],
            ]);            
        }

        return view('miningtax.user.display.details.invoice')->with('ores', $ores)
                                                             ->with('moons', $moons)
                                                             ->with('invoice', $invoice)
                                                             ->with('totalPrice', $totalPrice);
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
            $unpaidAmount += $un->invoice_amount;
        }

        //Total up the paid invoices
        foreach($paid as $p) {
            $paidAmount += $p->invoice_amount;
        }

        return view('miningtax.user.display.invoices.invoices')->with('unpaid', $unpaid)
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
            //Add the information into the structures array to go to the page to be displayed
            array_push($structures, [
                'structure_name' => $sName->structure_name,
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
        return view('miningtax.user.display.pulls.upcoming')->with('structures', $structures)
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
        
        //Get the corporation information from the character id
        $corpInfo = $lookup->GetCorporationInfo($character->corporation_id);
        
        //Get the observers from the database
        $observers = Observer::all();

        //Get the ledgers for each structure one at a time
        foreach($observers as $obs) {
            //Get the structure information
            $structureInfo = $sHelper->GetStructureInfo($obs->observer_id);

            //Add the name to the structures array
            array_push($structures, $structureInfo->name);
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
                        'ore' => $ledger->ore_name,
                        'quantity' => $ledger->quantity,
                        'updated' => $ledger->last_updated,
                    ]);
                }
            }
        }

        //Return the view
        return view('miningtax.user.display.details.ledger')->with('miningLedgers', $miningLedgers)
                                                    ->with('structures', $structures);
    }
}
