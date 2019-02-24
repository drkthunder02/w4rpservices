<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\Moon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;

use App\Library\Moons\MoonCalc;

class MoonsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    /**
     * Function to display the moons and pass data to the blade template
     */
    public function displayMoons() {
        //Get the user type from the user Auth class
        $type = Auth::user()->user_type;
        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Update the prices for the moon
        $moonCalc->FetchNewPrices();
        //get all of the moons from the database
        $moons = DB::table('Moons')->orderBy('System', 'asc')->get();
        //declare the html variable and set it to null
        $html = '';
        $table = array();
        $moonprice = null;
        foreach($moons as $moon) {
            //Setup formats as needed
            $spm = $moon->System . ' - ' . $moon->Planet . ' - ' . $moon->Moon;
            $rentalEnd = new Carbon($moon->RentalEnd);
            $rentalEnd = $rentalEnd->format('m-d');

            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                    $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

            $worth = $moonCalc->SpatialMoonsTotalWorth($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                       $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

            if($type == 'W4RP') {
                $moonprice = $price['alliance'];
            } else {
                $moonprice = $price['outofalliance'];
            }
            
            //Add the data to the html string to be passed to the view
            array_push($table, [
                'SPM' => $spm,
                'StructureName' => $moon->StructureName,
                'FirstOre' => $moon->FirstOre,
                'FirstQuantity' => $moon->FirstQuantity,
                'SecondOre' => $moon->SecondOre,
                'SecondQuantity' => $moon->SecondQuantity,
                'ThirdOre' => $moon->ThirdOre,
                'ThirdQuantity' => $moon->ThirdQuantity,
                'FourthOre' => $moon->FourthOre,
                'FourthQuantity' => $moon->FourthQuantity,
                'Price' => $moonprice,
                'Worth' => number_format($worth, "2", ".", ","),
                'RentalEnd' => $rentalEnd,
            ]);
        }

        return view('moons/user/moon')->with('table', $table);
    }

    public function displayTotalWorthForm() {
        return view('moons/user/formTotalWorth');
    }

    public function displayTotalWorth(Request $request) {
        $firstOre = $request->firstOre;
        $firstQuantity = $request->firstQuantity;
        $secondOre = $request->secondOre;
        $secondQuantity = $request->secondQuantity;
        $thirdOre = $request->thirdOre;
        $thirdQuantity = $request->thirdQuantity;
        $fourthOre = $request->fourthOre;
        $fourthQuantity = $request->fourthQuantity;

        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //Update the prices for the moon
        $moonCalc->FetchNewPrices();

        $totalGoo = $moonCalc->SpatialMoonsOnlyGooTotalWorth($firstOre, $firstQuantity, $secondOre, $secondQuantity,
                                                             $thirdOre, $thirdQuantity, $fourthOre, $fourthQuantity);
        $totalGoo = number_format($totalGoo, 2, ".", ",");

        $totalWorth = $moonCalc->SpatialMoonsTotalWorth($firstOre, $firstQuantity, $secondOre, $secondQuantity,
                                                        $thirdOre, $thirdQuantity, $fourthOre, $fourthQuantity);
        $totalWorth = number_format($totalWorth, 2, ".", ",");

        return view('moons.user.displayTotalWorth')->with(['totalWorth' => $totalWorth, 'totalGoo' => $totalGoo]);
    }
}
