<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\Moon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;
use App\Models\Moon\MoonRent;

use App\Models\Finances\PlayerDonationJournal;
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
        
        return view('moons.moonjournal')->with('journal', $journal);
    }

    public function updateMoon() {
        return view('moons.updatemoon');
    }

    public function storeUpdateMoon2(Request $request) {
        $moonCalc = MoonCalc();
        $lookup = LookupHelper();

        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
            'renter' => 'required',
            'date' => 'required',
            'contact' => 'required',
        ]);

        //Take the contact name and create a character id from it
        $contact = $lookup->CharacterNameToId($request->contact);

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
            'Contact' => $contact,
        ]);

        //Going to store moon price in a table for future reference
        MoonRent::insert([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
            'RentalCorp' => $request->renter,
            'RentalEnd' => $date,
            'Contact' => $request->contact,
            'Price' => $price,
        ]);

        return redirect('/moons/display')->with('success', 'Moon Updated');
    }

    public function storeUpdateMoon(Request $request) {
        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
            'renter' => 'required',
            'date' => 'required',
            'contact' => 'required',
        ]);

        $date = new Carbon($request->date . '00:00:01');
        //Update the database entry
        Moon::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
        ])->update([
            'RentalCorp' => $request->renter,
            'RentalEnd' => $date,
            'Contact' => $request->contact,
        ]);

        return redirect('/moons/display')->with('success', 'Moon Updated');
    }

    public function addMoon() {
        return view('moons.addmoon');
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

    /**
     * Function to display the moons to admins
     */
    public function displayMoonsAdmin() {
        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Update the prices for the moon
        $moonCalc->FetchNewPrices();
        //get all of the moons from the database
        $moons = Moon::orderBy('System', 'asc')->get();
        //declare the html variable and set it to null
        $html = '';
        foreach($moons as $moon) {
            //Setup formats as needed
            $spm = $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon;
            $rentalEnd = new Carbon($moon->RentalEnd);
            $rentalEnd = $rentalEnd->format('m-d');

            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                    $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
            //Add the data to the html string to be passed to the view
            $html .= '<tr>';
            $html .= '<td>' . $spm . '</td>';
            $html .= '<td>' . $moon->StructureName . '</td>';
            $html .= '<td>' . $moon->FirstOre . '</td>';
            $html .= '<td>' . $moon->FirstQuantity . '</td>';
            $html .= '<td>' . $moon->SecondOre . '</td>';
            $html .= '<td>' . $moon->SecondQuantity . '</td>';
            $html .= '<td>' . $moon->ThirdOre . '</td>';
            $html .= '<td>' . $moon->ThirdQuantity . '</td>';
            $html .= '<td>' . $moon->FourthOre . '</td>';
            $html .= '<td>' . $moon->FourthQuantity . '</td>';
            $html .= '<td>' . $price['alliance'] . '</td>';
            $html .= '<td>' . $price['outofalliance'] . '</td>';
            $html .= '<td>' . $moon->RentalCorp . '</td>';
            $html .= '<td>' . $rentalEnd . '</td>';
            $html .= '</tr>';
        }

        return view('moons.adminmoon')->with('html', $html);
    }
}
