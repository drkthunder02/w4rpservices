<?php

namespace App\Http\Controllers;

//Internal Library
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

    public function storeUpdateMoonRental(Request $request) {
        $moonCalc = new MoonCalc;
        $lookup = new LookupHelper;

        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
            'renter' => 'required',
            'date' => 'required',
            'contact' => 'required',
        ]);

        //Take the  contact name and create a character_id from it
        if($request->contact == 'None') {
            $contact = 'None';
        } else {
            $contact = $lookup->CharacterNameToId($request->contact);
        }

        //Let's find the corporation and alliance information to ascertain whethery they are in Warped Intentions or another Legacy Alliance
        $allianceId = $lookup->LookupCorporation($lookup->LookupCharacter($contact));

        //Create the date
        $date = new Carbon($request->date . '00:00:01');

        //Insert or update the moon rental database entry
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

        //Redirect to the update moon page
        return redirect('/moons/admin/updatemoon')->with('success', 'Moon Updated');
    }

    public function storeUpdateMoon(Request $request) {
        $moonCalc = new MoonCalc();
        $lookup = new LookupHelper();

        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
            'renter' => 'required',
            'date' => 'required',
            'contact' => 'required',
        ]);

        //Take the contact name and create a character id from it
        if($request->contact == 'None') {
            $contact = 'None';
        } else {
            $contact = $lookup->CharacterNameToId($request->contact);
        }
        //Let's find the corporation and alliance information to ascertain whether they are in Warped Intentions or another Legacy Alliance
        $corpId = $lookup->LookupCharacter($contact);
        $allianceId = $lookup->LookupCorporation($corpId);

        //Create the date
        $date = new Carbon($request->date . '00:00:01');
        //Calculate the moon price
        $moon = Moon::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
        ])->first();
        $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
        
        $date = new Carbon($request->date . '00:00:01');
        //Update the database entry
        Moon::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
        ])->update([
            'RentalCorp' => $request->renter,
            'RentalEnd' => $date,
        ]);

        //Going to store moon price in a table for future reference
        //We need to insert a price based on whether part of Legacy or part of Warped Intentions
        //Will need an if then else statement to complete this operation
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
            ]);
        }
        
        return redirect('/moons/admin/updatemoon')->with('success', 'Moon Updated');
    }

    /**
     * Function to display the moons to admins
     */
    public function displayMoonsAdmin() {
        $lookup = new LookupHelper;
        $contact = '';

        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Update the prices for the moon
        $moonCalc->FetchNewPrices();
        //get all of the moons from the database
        $moons = Moon::orderBy('System', 'asc')->get();
        //declare the html variable and set it to null
        $table = array();
        foreach($moons as $moon) {
            //Get the rental data for the moon
            $rental = MoonRental::where([
                'System' => $moon->System,
                'Planet' => $moon->Planet,
                'Moon' => $moon->Moon,
            ])->first();

            //Calculate hte price of the moon based on what is in the moon
            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

            //Set the rental end date
            $rentalEnd = new Carbon($rental->RentalEnd);
            //Set the rental end date as month / day
            $rentalEnd = $rentalTemp->format('m-d');                        

            //Set the paid as yes or no for the check box in the blade template
            $paid = $moon->Paid;

            //We need the contact information in character name format for the view
            $contact = MoonRental::where([
                'System' => $moon->System,
                'Planet' => $moon->Planet,
                'Moon' => $moon->Moon,
                'RentalEnd' => $moon->RentalEnd,
            ])->first();

            if($contact != null) {
                $contact = $contact->toArray();
                $contact = $lookup->CharacterName($contact['Contact']);
            } else {
                $contact = 'None';
            }

            //Set the color for the table
            if($rentalTemp->diffInDays(Carbon::now()) < 3 ) {
                $color = 'table-warning';
            } else if( $today > $rentalTemp) {
                $color = 'table-primary';
            } else {
                $color = 'table-danger';
            }
            
            //Add the data to the html string to be passed to the view
            array_push($table, [
                'SPM' => $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon,
                'StructureName' => $moon->StructureName,
                'AlliancePrice' => $price['alliance'],
                'OutOfAlliancePrice' => $price['outofalliance'],
                'Renter' => $moon->RentalCorp,
                'RentalEnd' => $rentalEnd,
                'RowColor' => $color,
                'Paid' => $paid,
                'Contact' => $contact,
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
        Moon::where([
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
}
