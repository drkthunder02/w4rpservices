<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Moon;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class MoonsController extends Controller
{
    public function displayMoons() {
        $moons = DB::table('moons')->get();
        
        return view('dashboard.moon.moon')->with('moons', $moons);
    }

    public function addMoon() {
        return view('dashboard.addmoon');
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
            'name' => 'required',
            'renter' => 'required',
            'date' => 'required'
        ]);

        $date = strtotime($request->date . '00:00:01');

        DB::table('moons')
            ->where('StructureName', $request->name)
            ->update([
                'RentalCorp' => $request->renter,
                'RentalEnd' => $date,
            ]);

        return redirect('/dashboard')->with('success', 'Moon Updated');
    }
}
