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

    public function showJournalEntries() {
        $dateInit = Carbon::now();
        $date = $dateInit->subDays(30);

        $journal = DB::select('SELECT amount,reason,description,date FROM `player_donation_journal` WHERE corporation_id=98287666 AND date >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH) ORDER BY date DESC');
        
        return view('moons.admin.moonjournal')->with('journal', $journal);
    }

    public function updateMoon() {
        //Return the view and the form from the blade display
        return view('moons.admin.updatemoon');
    }

    public function storeUpdateMoon(Request $request) {
        $moonCalc = new MoonCalc;
        $lookup = new LookupHelper;
        $paid = false;

        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
            'renter' => 'required',
            'date' => 'required',
            'contact' => 'required',
        ]);

        if($request->removal == true) {
            $this->RemoveRenter($request->system, $request->planet, $request->moon);
            return redirect('/moons/admin/updatemoon')->with('success', 'Moon Updated and Renter Removed.');
        }

        //Take the  contact name and create a character_id from it
        if($request->contact == 'None') {
            $contact = 'None';
        } else {
            $contact = $lookup->CharacterNameToId($request->contact);
        }

        if($request->paid == 'Yes') {
            $paid = 'Yes';
        } else {
            $paid = 'No';
        }

        if(isset($request->Paid_Until)) {
            $paidUntil = $request->Paid_Until;
        } else {
            $paidUntil = null;
        }

        //Let's find the corporation and alliance information to ascertain whethery they are in Warped Intentions or another Legacy Alliance
        $allianceId = $lookup->LookupCorporation($lookup->LookupCharacter($contact));

        //Create the date
        $date = new Carbon($request->date . '00:00:01');

        $count = MoonRental::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
            'Contact' => $contact,
        ])->count();
        dd($count);

        $found = MoonRental::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
            'Contact' => $contact,
        ])->first();

        //Calculate the price of the moon for when it's updated
        $moon = Moon::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
        ])->first();

        $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
        
        if(($found != null) && $request->removal != true) {
            if($allianceId = 99004116) {
                MoonRental::where([
                    'System' => $request->system,
                    'Planet' => $request->planet,
                    'Moon' => $request->moon,
                    'Contact' => $contact,
                ])->update([
                    'System' => $request->system,
                    'Planet' => $request->planet,
                    'Moon' => $request->moon,
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
                    'System' => $request->system,
                    'Planet' => $request->planet,
                    'Moon' => $request->moon,
                    'Contact' => $contact,
                ])->update([
                    'System' => $request->system,
                    'Planet' => $request->planet,
                    'Moon' => $request->moon,
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
                'System' => $request->system,
                'Planet' => $request->planet,
                'Moon' => $request->moon,
            ])->delete();
            
            if($allianceId = 99004116) {
                MoonRental::insert([
                    'System' => $request->system,
                    'Planet' => $request->planet,
                    'Moon' => $request->moon,
                    'RentalCorp' => $request->renter,
                    'RentalEnd' => $date,
                    'Contact' => $contact,
                    'Price' => $price['alliance'],
                    'Type' => 'alliance',
                    'Paid' => 'No',
                ]);
            } else {
                MoonRental::insert([
                    'System' =>$request->system,
                    'Planet' => $request->planet,
                    'Moon' => $request->moon,
                    'RentalCorp' => $request->renter,
                    'RentalEnd' => $date,
                    'Contact' => $contact,
                    'Price' => $price['outofalliance'],
                    'Type' => 'outofalliance',
                    'Paid' => 'No',
                ]);
            }
        }

        //Redirect to the update moon page
        return redirect('/moons/admin/updatemoon')->with('success', 'Moon Updated');
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
            $rental = MoonRental::where([
                'System' => $moon->System,
                'Planet' => $moon->Planet,
                'Moon' => $moon->Moon,
            ])->first();

            //Check if their is a current rental for a moon going on
            if($rental == false) {
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
                $color = 'table-primary';
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

    public function UpdateMoonPaid(Request $request) {
        $this->validate($request, [
            'paid' => 'required',
        ]);

        $str_array = explode(" - ", $request->paid);

        //Decode the value for the SPM into a system, planet, and moon for the database to update
        $system = $str_array[0];
        $planet = $str_array[1];
        $moon = $str_array[2];

        //Update the paid status of the moon
        MoonRental::where([
            'System' => $system,
            'Planet' => $planet,
            'Moon' => $moon,
        ])->update([
            'Paid' => 'Yes',
        ]);

        //Redirect back to the moon page, which should call the page to be displayed correctly
        return redirect('/moons/admin/display');
    }

    /**
     * Display function for adding a new rental moon to the pool
     * 
     */
    public function addMoon() {
        return view('moons.admin.addmoon');
    }

    /**
     * Add a new moon into the database
     * 
     * @return \Illuminate\Http\Reponse
     */
    public function storeMoon(Request $request) {
        $this->validate($request, [
            'region' => 'required',
            'system' => 'required',
            'structure' => 'required',
        ]);

        if($request->input('firstquan') < 1.00) {
            $firstQuan = $request->input('firstquan') * 100.00;
        } else {
            $firstQuan = $request->input('firstquan');
        }

        if($request->input('secondquan') < 1.00) {
            $firstQuan = $request->input('secondquan') * 100.00;
        } else {
            $firstQuan = $request->input('secondquan');
        }

        if($request->input('thirdquan') < 1.00) {
            $firstQuan = $request->input('thirdquan') * 100.00;
        } else {
            $firstQuan = $request->input('thirdquan');
        }

        if($request->input('fourthquan') < 1.00) {
            $firstQuan = $request->input('fourthquan') * 100.00;
        } else {
            $firstQuan = $request->input('fourthquan');
        }

        // Add new moon
        $moon = new Moon;
        $moon->Region = $request->input('region');
        $moon->System = $request->input('system');
        $moon->Planet = $request->input('planet');
        $moon->Moon = $request->input('moon');
        $moon->StructureName = $request->input('structure');
        $moon->FirstOre = $request->input('firstore');
        $moon->FirstQuantity = $request->input('firstquan');
        $moon->SecondOre = $request->input('secondore');
        $moon->SecondQuantity = $request->input('secondquan');
        $moon->ThirdOre = $request->input('thirdore');
        $moon->ThirdQuantity = $request->input('thirdquan');
        $moon->FourthOre = $request->input('fourthore');
        $moon->FourthQuantity = $request->input('fourthquan');
        $moon->save();

        return redirect('/dashboard')->with('success', 'Moon Added');
    }

    private function RemoveRenter($system, $planet, $moon) {
        $found = MoonRental::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
            'Contact' => $contact,
        ])->first();

        if($found) {
            MoonRental::where([
                'System' => $request->system,
                'Planet' => $request->planet,
                'Moon' => $request->moon,
            ])->delete();

            MoonRental::insert([
                'System' => $request->system,
                'Planet' => $request->planet,
                'Moon' => $request->moon,
                'Contact' => 'None',
                'Paid' => 'No',
                'Paid_Until' => Carbon::now()->endOfMonth(),
            ]);
        } else {
            MoonRental::insert([
                'System' => $request->system,
                'Planet' => $request->planet,
                'Moon' => $request->moon,
                'Contact' => 'None',
                'Paid' => 'No',
                'Paid_Until' => Carbon::now()->endOfMonth(),
            ]);
        }
    }
}
