<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

//Models
use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;
use App\Models\Moon\AllianceMoon;
use App\Models\MoonRentals\AllianceRentalMoon;
use App\Models\Moon\AllianceMoonRequest;

//Library
use App\Library\Moons\MoonCalc;
use App\Library\Lookups\LookupHelper;

class MoonsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Renter');
    }

    /**
     * Function to display all alliance moons and pass data to the blade template
     */
    public function displayMoons() {
        //Setup variables for moons
        $moons = array();
        $systems = array();

        //Get all of the alliance moon systems from the database
        $systems = AllianceMoon::groupBy('System')->pluck('System');

        //Get all of the alliance moons from the database
        $moons = AllianceMoon::all();

        $gasGoo = [
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

        return view('moons.user.allmoons')->with('systems', $systems)
                                          ->with('moons', $moons)
                                          ->with('gasGoo', $gasGoo)
                                          ->with('r8Goo', $r8Goo)
                                          ->with('r16Goo', $r16Goo)
                                          ->with('r32Goo', $r32Goo)
                                          ->with('r64Goo', $r64Goo);
    }

    /**
     * Function to display moon request form
     */
    public function displayRequestAllianceMoon() {
        return view('moons.user.requestmoon');
    }

    /**
     * Function to store the moon request
     */
    public function storeRequestAllianceMoon(Request $request) {
        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
        ]);

        //Declare some necessary arrays for figuring out what region a moon resides in
        $catch = [
            '6X7-JO',
            'A803-L',
            'I8-D0G',
            'WQH-4K',
            'GJ0-JO',
            'Q-S7ZD',
            'JWZ2-V',
            'J-ODE7',
            'OGL8-Q',
            'R-K4QY',
        ];

        $immensea = [
            'ZBP-TP',
            'XVV-21',
            'B9E-H6',
            'JDAS-0',
            'Y19P-1',
            'LN-56V',
            'O7-7UX',
            'Y2-QUV',
            'SPBS-6',
            'A4B-V5',
            'GXK-7F',
            '78TS-Q',
            'CJNF-J',
            'EA-HSA',
            'FYI-49',
            'WYF8-8',
            'NS2L-4',
            'B-S347',
            'AF0-V5',
            'QI-S9W',
            'B-A587',
            'PPFB-U',
            'L-5JCJ',
            '4-GB14',
            'REB-KR',
            'QE-E1D',
            'LK1K-5',
            'Z-H2MA',
            'B-KDOZ',
            'E8-YS9',
        ];

        //Declare lookup variables
        $lookup = new LookupHelper;

        //Get all of the information needed to create the database entry
        $charId = auth()->user()->getId();
        $charInfo = $lookup->GetCharacterInfo($charId);
        $charName = $charInfo->name;
        $corpInfo = $lookup->GetCorporationInfo($charInfo->corporation_id);
        $corpId = $corpInfo->corporation_id;
        $corpName = $corpInfo->name;
        $corpTicker = $corpInfo->ticker;
        //Declare the region variable as null for the lookup if statement
        $region = null;

        //Get the region the moon resides in from the system
        if(in_array($request->system, $catch, true)) {
            $region = 'Catch';
        } else if(in_array($request->system, $immensea, true)) {
            $region = 'Immensea';
        } else {
            //False value. Redirect back to page
            return redirct('/moons/display/request')->with('error', 'Region was not found.');
        }

        //Check to see if the moon has been previously inputted for
        $allMoons = AllianceMoon::all();
        foreach($allMoons as $moon) {
            if($moon->Region == $region && $moon->System == $request->system && $moon->Planet == $request->planet && $moon->Moon == $request->moon) {
                if($moon->Availability != 'Available') {
                    return redirect('moons/display/request')->with('error', 'The moon has already been reserved by another party.');
                }

                break;
            }
        }

        //Create the new object to save into the database
        $moonRequest = new AllianceMoonRequest;
        $moonRequest->region = $region;
        $moonRequest->system = $request->system;
        $moonRequest->planet = $request->planet;
        $moonRequest->moon = $request->moon;
        $moonRequest->corporation_id = $corpId;
        $moonRequest->corporation_name = $corpName;
        $moonRequest->corporation_ticker = $corpTicker;
        $moonRequest->requestor_name = $charName;
        $moonRequest->requestor_id = $charId;
        $moonRequest->status = 'Pending';
        $moonRequest->save();

        //Update the current moon's status in the model AllianceMoon
        AllianceMoon::where([
            'Region' => $region,
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
        ])->update([
            'Availability' => 'Request Pending',
        ]);

        return redirect('/moons/display/request')->with('success', 'Moon request submitted.');
    }

    /**
     * Function to display the moons and pass to the blade template
     * Function description will be updated in a future release.
     */
    public function displayRentalMoons() {
        //Declare variables
        $rentalEnd = null;
        $lastMonth = Carbon::now()->subMonth();
        $today = Carbon::now();
        $table = array();
        $moonprice = null;

        //Get the user type from the user Auth class
        $type = auth()->user()->getUserType();
        //Get all of the rental moons from the database
        $moons = AllianceRentalMoon::orderBy('system', 'ASC')
                                   ->orderBy('planet', 'ASC')
                                   ->orderBy('moon', 'ASC')
                                   ->get();
        
        //For each of the moons let's format the data for the display table
        foreach($moons as $moon) {
            //Check if someone is currently renting the moon
            if(($moon->rental_type == 'In Alliance' || $moon->rental_type == 'Out of Alliance') && ($moon->paid == 'Yes')) {
                $rentalTemp = new Carbon($moon->rental_until);
                $rentalEnd = $rentalTemp->format('m-d');
            } else if($moon->rental_type == 'Alliance') {
                $rentalTemp = $today->endOfMonth();
                $rentalEnd = $rentalTemp->format('m-d');
            } else {
                //Set the rental date for if someone is not renting the moon
                $rentalTemp = $lastMonth;
                $rentalEnd = $rentalTemp->format('m-d');
            }

            //Get the price of the moon from the database based on if the person is in Warped Intentions
            $userType = auth()->user()->getUserType();
            if($userType == 'W4RP') {
                $moonprice = $moon->alliance_rental_price;
            } else {
                $moonprice = $moon->out_of_alliance_rental_price;
            }

            //Get the color correct for the table
            if($moon->rental_type == 'In Alliance' || $moon->rental_type == 'Out of Alliance') {
                if($rentalTemp->diffInDays($today) < 3 && $today < $rentalTemp ) {
                    $color = 'table-danger';
                } else if($today > $rentalTemp) {
                    $color = 'table-primary';
                } else if($today < $rentalTemp) {
                    $color = 'table-danger';
                }
            } else if($moon->rental_type == 'Alliance') {
                $color = 'table-info';
            } else {
                $color = 'table-primary';
            }            

            //Add the data to the html array to be passed to the view
            array_push($table, [
                'SPM' => $moon->system . " - " . $moon->planet . " - " . $moon->moon,
                'StructureName' => $moon->structure_name,
                'FirstOre' => $moon->first_ore,
                'FirstQuantity' => number_format($moon->first_quantity, 0, ".", ","),
                'SecondOre' => $moon->second_ore,
                'SecondQuantity' => number_format($moon->second_quantity, 0, ".", ","),
                'ThirdOre' => $moon->third_ore,
                'ThirdQuantity' => number_format($moon->third_quantity, 0, ".", ","),
                'FourthOre' => $moon->fourth_ore,
                'FourthQuantity' => number_format($moon->fourth_quantity, 0, ".", ","),
                'Price' => number_format($moonprice, 0, ".", ","),
                'Worth' => number_format($moon->moon_worth, 0, ".", ","),
                'RentalEnd' => $rentalEnd,
                'RowColor' => $color,
            ]);
        }

        //Pass the data to the view
        return view('moons.user.moon')->with('table', $table);
    }

    public function displayTotalWorthForm() {
        return view('moons.user.formTotalWorth');
    }

    public function displayTotalWorth(Request $request) {
        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        $totalPull = 5.55 * (3600.00 * 24.00 * 30.00);
        $composition = [
            'Tritanium' => 0,
            'Pyerite' => 0,
            'Mexallon' => 0,
            'Isogen' => 0,
            'Nocxium' => 0,
            'Zydrine' => 0,
            'Megacyte' => 0,
            'Atmospheric_Gases' => 0,
            'Evaporite_Deposits' => 0,
            'Hydrocarbons' => 0,
            'Silicates' => 0,
            'Cobalt' => 0,
            'Scandium' => 0,
            'Titanium' => 0,
            'Tungsten' => 0,
            'Cadmium' => 0,
            'Platinum' => 0,
            'Vanadium'=> 0,
            'Chromium' => 0,
            'Technetium' => 0,
            'Hafnium' => 0,
            'Caesium' => 0,
            'Mercury' => 0,
            'Dysprosium' => 0,
            'Neodymium' => 0,
            'Promethium' => 0,
            'Thulium' => 0,
        ];

        $firstOre = $request->firstOre;
        if($request->firstQuantity >= 1.00) {
            $firstQuantity = $request->firstQuantity / 100.00;
        } else {
            $firstQuantity = $request->firstQuantity;
        }
        
        $secondOre = $request->secondOre;
        if($request->secondQuantity >= 1.00) {
            $secondQuantity = $request->secondQuantity / 100.00;
        } else {
            $secondQuantity = $request->secondQuantity;
        }

        $thirdOre = $request->thirdOre;
        if($request->thirdQuantity >= 1.00) {
            $thirdQuantity = $request->thirdQuantity / 100.00;
        } else {
            $thirdQuantity = $request->thirdQuantity;
        }

        $fourthOre = $request->fourthOre;
        if($request->fourthQuantity >= 1.00) {
            $fourthQuantity = $request->fourthQuantity / 100.00;
        } else {
            $fourthQuantity = $request->fourthQuantity;
        }

        if($request->reprocessing >= 1.00) {
            $reprocessing = $request->reprocessing / 100.00;
        } else {
            $reprocessing = $request->reprocessing;
        }

        //Set the reprocessing level for 84% if the value is null
        if($request->reprocessing == null) {
            $reprocessing = 0.84;
        }

        //Get the total percentage of the ores, and normalize it to 1.00.
        $totalPercent = $firstQuantity + $secondQuantity + $thirdQuantity + $fourthQuantity;
        if($totalPercent < 1.00) {
            $firstQuantity = $firstQuantity / $totalPercent;
            $secondQuantity = $secondQuantity / $totalPercent;
            $thirdQuantity = $thirdQuantity / $totalPercent;
            $fourthQuantity = $fourthQuantity / $totalPercent;
        }

        //Calculate the total worth of the moon
        $totalWorth = $moonCalc->SpatialMoonsTotalWorth($firstOre, $firstQuantity, $secondOre, $secondQuantity,
                                                        $thirdOre, $thirdQuantity, $fourthOre, $fourthQuantity);
        //Format the number to send to the blade.
        $totalWorth = number_format($totalWorth, 2, ".", ",");

        //Get the composition for the first ore if it is not None.
        //Add the first ore composition to the final composition
        if($firstOre != 'None') {
            //Get the ore's composition
            $firstComp = $moonCalc->GetOreComposition($firstOre);
            //Get the amount of units mine-able from the moon
            $mUnits = $moonCalc->CalcOreUnits($firstOre, $firstQuantity);
            //Calculate the number of reprocessing units to happen from moon units
            $rUnits = floor($mUnits / 100.0);

            //Compile the composition of the ore
            $composition['Tritanium'] += floor(($firstComp->Tritanium * $rUnits) * $reprocessing);
            $composition['Pyerite'] += floor(($firstComp->Pyerite * $rUnits) * $reprocessing);
            $composition['Mexallon'] += floor(($firstComp->Mexallon * $rUnits) * $reprocessing);
            $composition['Isogen'] += floor(($firstComp->Isogen * $rUnits) * $reprocessing);
            $composition['Nocxium'] += floor(($firstComp->Nocxium * $rUnits) * $reprocessing);
            $composition['Zydrine'] += floor(($firstComp->Zydrine * $rUnits) * $reprocessing);
            $composition['Megacyte'] += floor(($firstComp->Megacyte * $rUnits) * $reprocessing);
            $composition['Atmospheric_Gases'] += floor(($firstComp->AtmosphericGases * $rUnits) * $reprocessing);
            $composition['Evaporite_Deposits'] += floor(($firstComp->EvaporiteDeposits * $rUnits) * $reprocessing);
            $composition['Hydrocarbons'] += floor(($firstComp->Hydrocarbons * $rUnits) * $reprocessing);
            $composition['Silicates'] += floor(($firstComp->Silicates * $rUnits) * $reprocessing);
            $composition['Cobalt'] += floor(($firstComp->Cobalt * $rUnits) * $reprocessing);
            $composition['Scandium'] += floor(($firstComp->Scandium * $rUnits) * $reprocessing);
            $composition['Titanium'] += floor(($firstComp->Titanium * $rUnits) * $reprocessing);
            $composition['Tungsten'] += floor(($firstComp->Tungsten * $rUnits) * $reprocessing);
            $composition['Cadmium'] += floor(($firstComp->Cadmium * $rUnits) * $reprocessing);
            $composition['Platinum'] += floor(($firstComp->Platinium * $rUnits) * $reprocessing);
            $composition['Vanadium'] += floor(($firstComp->Vanadium * $rUnits) * $reprocessing);
            $composition['Chromium'] += floor(($firstComp->Chromium * $rUnits) * $reprocessing);
            $composition['Technetium'] += floor(($firstComp->Technetium * $rUnits) * $reprocessing);
            $composition['Hafnium'] += floor(($firstComp->Hafnium * $rUnits) * $reprocessing);
            $composition['Caesium'] += floor(($firstComp->Caesium * $rUnits) * $reprocessing);
            $composition['Mercury'] += floor(($firstComp->Mercury * $rUnits) * $reprocessing);
            $composition['Dysprosium'] += floor(($firstComp->Dysprosium * $rUnits) * $reprocessing);
            $composition['Neodymium'] += floor(($firstComp->Neodymium * $rUnits) * $reprocessing);
            $composition['Promethium'] += floor(($firstComp->Promethium * $rUnits) * $reprocessing);
            $composition['Thulium'] += floor(($firstComp->Thulium * $rUnits) * $reprocessing);
        }

        //Get the composition for the second ore if it is not None.
        //Add the second ore composition to the final composition
        if($secondOre != 'None') {
            //Get the ore's composition
            $secondComp = $moonCalc->GetOreComposition($secondOre);
            //Get the amount of units mine-able from the moon
            $mUnits = $moonCalc->CalcOreUnits($secondOre, $secondQuantity);
            //Calculate the number of reprocessing units to happen from moon units
            $rUnits = floor($mUnits / 100.0);            

            $composition['Tritanium'] += floor(($secondComp->Tritanium * $rUnits) * $reprocessing);
            $composition['Pyerite'] += floor(($secondComp->Pyerite * $rUnits) * $reprocessing);
            $composition['Mexallon'] += floor(($secondComp->Mexallon * $rUnits) * $reprocessing);
            $composition['Isogen'] += floor(($secondComp->Isogen * $rUnits) * $reprocessing);
            $composition['Nocxium'] += floor(($secondComp->Nocxium * $rUnits) * $reprocessing);
            $composition['Zydrine'] += floor(($secondComp->Zydrine * $rUnits) * $reprocessing);
            $composition['Megacyte'] += floor(($secondComp->Megacyte * $rUnits) * $reprocessing);
            $composition['Atmospheric_Gases'] += floor(($secondComp->AtmosphericGases * $rUnits) * $reprocessing);
            $composition['Evaporite_Deposits'] += floor(($secondComp->EvaporiteDeposits * $rUnits) * $reprocessing);
            $composition['Hydrocarbons'] += floor(($secondComp->Hydrocarbons * $rUnits) * $reprocessing);
            $composition['Silicates'] += floor(($secondComp->Silicates * $rUnits) * $reprocessing);
            $composition['Cobalt'] += floor(($secondComp->Cobalt * $rUnits) * $reprocessing);
            $composition['Scandium'] += floor(($secondComp->Scandium * $rUnits) * $reprocessing);
            $composition['Titanium'] += floor(($secondComp->Titanium * $rUnits) * $reprocessing);
            $composition['Tungsten'] += floor(($secondComp->Tungsten * $rUnits) * $reprocessing);
            $composition['Cadmium'] += floor(($secondComp->Cadmium * $rUnits) * $reprocessing);
            $composition['Platinum'] += floor(($secondComp->Platinium * $rUnits) * $reprocessing);
            $composition['Vanadium'] += floor(($secondComp->Vanadium * $rUnits) * $reprocessing);
            $composition['Chromium'] += floor(($secondComp->Chromium * $rUnits) * $reprocessing);
            $composition['Technetium'] += floor(($secondComp->Technetium * $rUnits) * $reprocessing);
            $composition['Hafnium'] += floor(($secondComp->Hafnium * $rUnits) * $reprocessing);
            $composition['Caesium'] += floor(($secondComp->Caesium * $rUnits) * $reprocessing);
            $composition['Mercury'] += floor(($secondComp->Mercury * $rUnits) * $reprocessing);
            $composition['Dysprosium'] += floor(($secondComp->Dysprosium * $rUnits) * $reprocessing);
            $composition['Neodymium'] += floor(($secondComp->Neodymium * $rUnits) * $reprocessing);
            $composition['Promethium'] += floor(($secondComp->Promethium * $rUnits) * $reprocessing);
            $composition['Thulium'] += floor(($secondComp->Thulium * $rUnits) * $reprocessing);
        }
        
        //Get the composition for the third ore if it is not None.
        //Add the third ore composition to the final composition
        if($thirdOre != 'None') {
            //Get the ore's composition
            $thirdComp = $moonCalc->GetOreComposition($thirdOre);
            //Get the amount of units mine-able from the moon
            $mUnits = $moonCalc->CalcOreUnits($thirdOre, $thirdQuantity);
            //Calculate the number of reprocessing units to happen from moon units
            $rUnits = floor($mUnits / 100.0);            

            $composition['Tritanium'] += floor(($thirdComp->Tritanium * $rUnits) * $reprocessing);
            $composition['Pyerite'] += floor(($thirdComp->Pyerite * $rUnits) * $reprocessing);
            $composition['Mexallon'] += floor(($thirdComp->Mexallon * $rUnits) * $reprocessing);
            $composition['Isogen'] += floor(($thirdComp->Isogen * $rUnits) * $reprocessing);
            $composition['Nocxium'] += floor(($thirdComp->Nocxium * $rUnits) * $reprocessing);
            $composition['Zydrine'] += floor(($thirdComp->Zydrine * $rUnits) * $reprocessing);
            $composition['Megacyte'] += floor(($thirdComp->Megacyte * $rUnits) * $reprocessing);
            $composition['Atmospheric_Gases'] += floor(($thirdComp->AtmosphericGases * $rUnits) * $reprocessing);
            $composition['Evaporite_Deposits'] += floor(($thirdComp->EvaporiteDeposits * $rUnits) * $reprocessing);
            $composition['Hydrocarbons'] += floor(($thirdComp->Hydrocarbons * $rUnits) * $reprocessing);
            $composition['Silicates'] += floor(($thirdComp->Silicates * $rUnits) * $reprocessing);
            $composition['Cobalt'] += floor(($thirdComp->Cobalt * $rUnits) * $reprocessing);
            $composition['Scandium'] += floor(($thirdComp->Scandium * $rUnits) * $reprocessing);
            $composition['Titanium'] += floor(($thirdComp->Titanium * $rUnits) * $reprocessing);
            $composition['Tungsten'] += floor(($thirdComp->Tungsten * $rUnits) * $reprocessing);
            $composition['Cadmium'] += floor(($thirdComp->Cadmium * $rUnits) * $reprocessing);
            $composition['Platinum'] += floor(($thirdComp->Platinium * $rUnits) * $reprocessing);
            $composition['Vanadium'] += floor(($thirdComp->Vanadium * $rUnits) * $reprocessing);
            $composition['Chromium'] += floor(($thirdComp->Chromium * $rUnits) * $reprocessing);
            $composition['Technetium'] += floor(($thirdComp->Technetium * $rUnits) * $reprocessing);
            $composition['Hafnium'] += floor(($thirdComp->Hafnium * $rUnits) * $reprocessing);
            $composition['Caesium'] += floor(($thirdComp->Caesium * $rUnits) * $reprocessing);
            $composition['Mercury'] += floor(($thirdComp->Mercury * $rUnits) * $reprocessing);
            $composition['Dysprosium'] += floor(($thirdComp->Dysprosium * $rUnits) * $reprocessing);
            $composition['Neodymium'] += floor(($thirdComp->Neodymium * $rUnits) * $reprocessing);
            $composition['Promethium'] += floor(($thirdComp->Promethium * $rUnits) * $reprocessing);
            $composition['Thulium'] += floor(($thirdComp->Thulium * $rUnits) * $reprocessing);
        }

        //Get the composition for the fourth ore if it is not None.
        //Add the fourth ore composition to the final composition
        if($fourthOre != 'None') {
            //Get the ore's composition
            $fourthComp = $moonCalc->GetOreComposition($fourthOre);
            //Get the amount of units mine-able from the moon
            $mUnits = $moonCalc->CalcOreUnits($fourthOre, $fourthQuantity);
            //Calculate the number of reprocessing units to happen from moon units
            $rUnits = floor($mUnits / 100.0);            

            $composition['Tritanium'] += floor(($fourthComp->Tritanium * $rUnits) * $reprocessing);
            $composition['Pyerite'] += floor(($fourthComp->Pyerite * $rUnits) * $reprocessing);
            $composition['Mexallon'] += floor(($fourthComp->Mexallon * $rUnits) * $reprocessing);
            $composition['Isogen'] += floor(($fourthComp->Isogen * $rUnits) * $reprocessing);
            $composition['Nocxium'] += floor(($fourthComp->Nocxium * $rUnits) * $reprocessing);
            $composition['Zydrine'] += floor(($fourthComp->Zydrine * $rUnits) * $reprocessing);
            $composition['Megacyte'] += floor(($fourthComp->Megacyte * $rUnits) * $reprocessing);
            $composition['Atmospheric_Gases'] += floor(($fourthComp->AtmosphericGases * $rUnits) * $reprocessing);
            $composition['Evaporite_Deposits'] += floor(($fourthComp->EvaporiteDeposits * $rUnits) * $reprocessing);
            $composition['Hydrocarbons'] += floor(($fourthComp->Hydrocarbons * $rUnits) * $reprocessing);
            $composition['Silicates'] += floor(($fourthComp->Silicates * $rUnits) * $reprocessing);
            $composition['Cobalt'] += floor(($fourthComp->Cobalt * $rUnits) * $reprocessing);
            $composition['Scandium'] += floor(($fourthComp->Scandium * $rUnits) * $reprocessing);
            $composition['Titanium'] += floor(($fourthComp->Titanium * $rUnits) * $reprocessing);
            $composition['Tungsten'] += floor(($fourthComp->Tungsten * $rUnits) * $reprocessing);
            $composition['Cadmium'] += floor(($fourthComp->Cadmium * $rUnits) * $reprocessing);
            $composition['Platinum'] += floor(($fourthComp->Platinium * $rUnits) * $reprocessing);
            $composition['Vanadium'] += floor(($fourthComp->Vanadium * $rUnits) * $reprocessing);
            $composition['Chromium'] += floor(($fourthComp->Chromium * $rUnits) * $reprocessing);
            $composition['Technetium'] += floor(($fourthComp->Technetium * $rUnits) * $reprocessing);
            $composition['Hafnium'] += floor(($fourthComp->Hafnium * $rUnits) * $reprocessing);
            $composition['Caesium'] += floor(($fourthComp->Caesium * $rUnits) * $reprocessing);
            $composition['Mercury'] += floor(($fourthComp->Mercury * $rUnits) * $reprocessing);
            $composition['Dysprosium'] += floor(($fourthComp->Dysprosium * $rUnits) * $reprocessing);
            $composition['Neodymium'] += floor(($fourthComp->Neodymium * $rUnits) * $reprocessing);
            $composition['Promethium'] += floor(($fourthComp->Promethium * $rUnits) * $reprocessing);
            $composition['Thulium'] += floor(($fourthComp->Thulium * $rUnits) * $reprocessing);
        }

        return view('moons.user.displayTotalWorth')->with('totalWorth', $totalWorth)
                                                   ->with('composition', $composition)
                                                   ->with('reprocessing', $reprocessing);
    }
}
