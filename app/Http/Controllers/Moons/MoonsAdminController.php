<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;

//Models
use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\Moon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;
use App\Models\MoonRent\MoonRental;

//Library
use App\Library\Moons\MoonCalc;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

class MoonsAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Function to display the moons to admins
     */
    public function displayMoonsAdmin() {
        $lookup = new LookupHelper;
        $contact = '';
        $paid = '';
        $rentalEnd = '';
        $renter = '';
        $ticker = '';

        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Get all of the moons from the database
        $moons = Moon::orderBy('System', 'asc')->get();
        //Declare the html variable and set it to null
        $table = array();
        //Set carbon dates as needed
        $lastMonth = Carbon::now()->subMonth();
        $today = Carbon::now();

        foreach($moons as $moon) {
            //Get the rental data for the moon
            $count = MoonRental::where([
                'System' => $moon->System,
                'Planet' => $moon->Planet,
                'Moon' => $moon->Moon,
            ])->count();

            //Check if their is a current rental for a moon going on
            if($count == 0) {
                //If we don't find a rental record, mark the moon as not paid
                $paid = 'No';

                //If we don't find a rental record, set the rental date as last month
                $rentalTemp = $lastMonth;
                $rentalEnd = $rentalTemp->format('m-d');

                //Set the contact info
                $contact = 'None';

                //Set the renter info
                $renter = 'None';

                //Set the ticker info
                $ticker = 'N/A';
            } else {
                //Get the rental data for the moon
                $rental = MoonRental::where([
                    'System' => $moon->System,
                    'Planet' => $moon->Planet,
                    'Moon' => $moon->Moon,
                ])->first();

                //If we find a rental record, mark the moon as whether it's paid or not
                $paid = $rental->Paid;

                //Set the rental date up
                $rentalTemp = new Carbon($rental->RentalEnd);
                $rentalEnd = $rentalTemp->format('m-d');

                //Set the contact name
                $contact = $lookup->CharacterName($rental->Contact);
                
                //Set up the renter whether it's W4RP or another corporation
                $corpId = $lookup->LookupCorporationId($rental->Contact);
                $allianceId = $lookup->LookupCorporation($corpId);
                $ticker = $lookup->LookupAllianceTicker($allianceId);
            }

            //Set the color for the table
            if($rentalTemp->diffInDays($today) < 3 ) {
                $color = 'table-warning';
            } else if( $today > $rentalTemp) {
                $color = 'table-success';
            } else {
                $color = 'table-danger';
            }

            //Calculate hte price of the moon based on what is in the moon
            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
            
            //Add the data to the html string to be passed to the view
            array_push($table, [
                'SPM' => $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon,
                'StructureName' => $moon->StructureName,
                'AlliancePrice' => $price['alliance'],
                'OutOfAlliancePrice' => $price['outofalliance'],
                'RentalEnd' => $rentalEnd,
                'RowColor' => $color,
                'Paid' => $paid,
                'Contact' => $contact,
                'Renter' => $ticker,
            ]);
        }

        return view('moons.admin.adminmoon')->with('table', $table);
    }

    /**
     * Function to remove a renter from a moon
     */
    public function storeMoonRemoval(Request $request) {
        $this->validate($request, [
            'remove' => 'required',
        ]);

        $str_array = explode(" - ", $request->remove);

        //Decode the value for the SPM into a system, planet, and moon for the database to update
        $system = $str_array[0];
        $planet = $str_array[1];
        $moon = $str_array[2];

        $found = MoonRental::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $moon,
        ])->count();

        if($found != 0) {
            MoonRental::where([
                'System' => $system,
                'Planet' => $planet,
                'Moon' => $moon,
            ])->delete();

            return redirect('/moons/admin/display')->with('success', 'Renter removed.');
        } 

        //Redirect back to the moon page, which should call the page to be displayed correctly
        return redirect('/moons/admin/display')->with('error', 'Something went wrong.');
    }

    /**
     * Function displays the ability for admins to update moons with who is renting, and when it ends.
     */
    public function updateMoon() {
        //Declare some variables
        $system = null;
        $planet = null;
        $moon = null;
        $name = null;
        $spmnTemp = array();
        $spmn = array();

        //Get the moons and put in order by System, Planet, then Moon number
        $moons = Moon::orderBy('System', 'ASC')
                       ->orderBy('Planet', 'ASC')
                       ->orderBy('Moon', 'ASC')
                       ->get();

        //Push our default value onto the stack
        array_push($spmn, 'N/A');

        //Form our array of strings for each system, planet, and moon combination.
        foreach($moons as $m) {
            $temp = $m->System . " - " . $m->Planet . " - " . $m->Moon . " - " . $m->StructureName;
            array_push($spmnTemp, $temp);
        }

        //From our temporary array with all the values in a numbered key, create the real array with the value being the key
        foreach($spmnTemp as $key => $value) {
            $spmn[$value] = $value;
        }

        //Return the view and the form from the blade display
        //Pass the data to the view as well
        return view('moons.admin.updatemoon')->with('spmn', $spmn);
    }

    public function storeUpdateMoon(Request $request) {
        //Declare some static variables as needed
        $moonCalc = new MoonCalc;
        $lookup = new LookupHelper;
        $paid = false;
        $system = null;
        $planet = null;
        $moon = null;
        $name = null;

        //Validate our request from the html form
        $this->validate($request, [
            'spmn' => 'required',
            'renter' => 'required',
            'date' => 'required',
            'contact' => 'required',
        ]);

        //Decode the System, Planet, Moon, Name combinatio sent from the controller
        $str_array = explode(" - ", $request->spmn);
        $system = $str_array[0];
        $planet = $str_array[1];
        $moon = $str_array[2];
        $name = $str_array[3];

        //Take the  contact name and create a character_id from it
        if($request->contact == 'None') {
            $contact = 'None';
        } else {
            $contact = $lookup->CharacterNameToId($request->contact);
        }

        //Update the paid value for database entry
        if($request->paid == 'Yes') {
            $paid = 'Yes';
        } else {
            $paid = 'No';
        }

        //Update the paid unti value for the database entry
        if(isset($request->Paid_Until)) {
            $paidUntil = $request->Paid_Until;
        } else {
            $paidUntil = null;
        }

        //Let's find the corporation and alliance information to ascertain whethery they are in Warped Intentions or another Legacy Alliance
        $allianceId = $lookup->LookupCorporation($lookup->LookupCharacter($contact));

        //Create the date
        $date = new Carbon($request->date . '00:00:01');

        //Count how many rentals we find for later database processing
        $count = MoonRental::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $moon,
            'Contact' => $contact,
        ])->count();

        //Calculate the price of the moon for when it's updated
        $moon = Moon::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $moon,
        ])->first();

        //Calculate the price of the rental and store it in the database
        $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
        
        //If the database entry isn't found, then insert it into the database,
        //otherwise, account for it being in the system already.

        dd($moon);
        if($count != 0) {
            if($allianceId = 99004116) {
                MoonRental::where([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $moon,
                    'Contact' => $contact,
                ])->update([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $moon,
                    'RentalCorp' => $request->renter,
                    'RentalEnd' => $date,
                    'Contact' => $contact,
                    'Price' => $price['alliance'],
                    'Type' => 'alliance',
                    'Paid' => $paid,
                    'Paid_Until' => $request->paid_until,
                ]);
            } else {
                MoonRental::where([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $moon,
                    'Contact' => $contact,
                ])->update([
                    'System' => $system,
                    'Planet' => $planet,
                    'Moon' => $moon,
                    'RentalCorp' => $request->renter,
                    'RentalEnd' => $date,
                    'Contact' => $contact,
                    'Price' => $price['outofalliance'],
                    'Type' => 'outofalliance',
                    'Paid' => $paid,
                    'Paid_Until' => $request->paid_until,
                ]);
            }
        } else {
            //If the entry is not found, then attempt to delete whatever existing data is there, then 
            //insert the new data
            MoonRental::where([
                'System' => $system,
                'Planet' => $planet,
                'Moon' => $moon,
            ])->delete();
            
            if($allianceId == 99004116) {
                $store = new MoonRental;
                $store->System = $system;
                $store->Planet = $planet;
                $store->Moon = $moon;
                $store->RentalCorp = $request->renter;
                $store->RentalEnd = $date;
                $store->Contact = $contact;
                $store->Price = $price['alliance'];
                $store->Type = 'alliance';
                $store->Paid = $paid;
                $store->save();
            } else {
                $store = new MoonRental;
                $store->System = $system;
                $store->Planet = $planet;
                $store->Moon = $moon;
                $store->RentalCorp = $request->renter;
                $store->RentalEnd = $date;
                $store->Contact = $contact;
                $store->Price = $price['outofalliance'];
                $store->Type = 'outofalliance';
                $store->Paid = $paid;
                $store->save();
            }
        }

        //Redirect to the update moon page
        return redirect('/moons/admin/updatemoon')->with('success', 'Moon Updated');
    }

    public function showJournalEntries() {
        $dateInit = Carbon::now();
        $date = $dateInit->subDays(30);

        $journal = DB::select('SELECT amount,reason,description,date FROM `player_donation_journal` WHERE corporation_id=98287666 AND date >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH) ORDER BY date DESC');
        
        return view('moons.admin.moonjournal')->with('journal', $journal);
    }
}
