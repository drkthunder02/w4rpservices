<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Carbon\Carbon;

use App\Models\Moon\Moon;

use App\Models\Finances\PlayerDonationJournal;
use App\Library\MoonCalc;
use App\Library\Esi;

class MoonsAdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    public function showJournalEntries() {
        $esi = new Esi();

        $dateInit = Carbon::now();
        $date = $dateInit->subDays(30);

        $journal = PlayerDonationJournal::whereRaw("corporation_id=98297666 AND date BETWEEN '" . $dateInit->toDateTimeString() . "' AND '" . $date->toDateTimeString())->get([
            'amount',
            'date',
            'description',
            'first_party_id',
        ]);

        foreach($journal as $journ) {
            $journ->first_party_id = $esi->GetCharacterName($journ->first_party_id);
        }

        return view('moons.spatialjournal')->with('journal', $journal);
    }

    public function updateMoon() {
        return view('moons.updatemoon');
    }

    public function storeUpdateMoon(Request $request) {
        $this->validate($request, [
            'system' => 'required',
            'planet' => 'required',
            'moon' => 'required',
            'renter' => 'required',
            'date' => 'required'
        ]);

        $date = strtotime($request->date . '00:00:01');
        //Update the database entry
        Moon::where([
            'System' => $request->system,
            'Planet' => $request->planet,
            'Moon' => $request->moon,
        ])->update([
            'RentalCorp' => $request->renter,
            'RentalEnd' => $date,
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
        //$moons = DB::table('Moons')->orderBy('System', 'asc')->get();
        //declare the html variable and set it to null
        $html = '';
        foreach($moons as $moon) {
            //Setup formats as needed
            $spm = $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon;
            $rentalEnd = date('m/d/Y', $moon->RentalEnd);
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
