<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Moon;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use App\Library\MoonCalc;

class MoonsController extends Controller
{
    /**
     * Function to display the moons and pass data to the blade template
     */
    public function displayMoons() {
        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Update the prices for the moon
        $moonCalc->FetchNewPrices();
        //get all of the moons from the database
        $moons = DB::table('Moons')->orderBy('System', 'asc')->get();
        //declare the html variable and set it to null
        $html = '';
        foreach($moons as $moon) {
            //Setup formats as needed
            $spm = $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon;
            $rentalEnd = date('d.m.Y', $moon->RentalEnd);
            $price = $moonCalc->SpatialMoons($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                             $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);
            $allyPrice = $moonCalc->SpatialMoonsOutOfAlliance($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
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
            $html .= '<td>' . $price . '</td>';
            $html .= '<td>' . $allyPrice . '</td>';
            $html .= '<td>' . $moon->RentalCorp . '</td>';
            $html .= '<td>' . $rentalEnd . '</td>';
            $html .= '</tr>';
        }

        return view('moons.moon')->with('html', $html);
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
        DB::table('Moons')
            ->whereColumn([$request->system, '=', 'System'], [$requeset->planet, '=', 'Planet'], [$request->moon, '=', 'Moon'])
            ->update([
                'RentalCorp' => $request->renter,
                'RentalEnd' => $date,
            ]);

        return redirect('/dashboard')->with('success', 'Moon Updated');
    }
}
