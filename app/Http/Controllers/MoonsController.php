<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;

use App\Models\Moon\Moon;

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
            if($type == 'W4RP') {
                $html .= '<td>' . $price['alliance'] . '</td>';
            } else if ($type == 'Legacy') {
                $html .= '<td>' . $price['outofalliance'] . '</td>';
            } else {
                $html .= '<td>N/A</td>';
            }
            $html .= '<td>' . $moon->RentalCorp . '</td>';
            $html .= '<td>' . $rentalEnd . '</td>';
            $html .= '</tr>';
        }

        return view('moons.moon')->with('html', $html);
    }

    public function displayTotalWorthForm() {
        return view('moons.formTotalWorth');
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

        $totalGoo = $moonCalc->SpatialMoonsOnlyGooTotalWorth($firstOre, $firstQuantity,
                                                             $secondOre, $secondQuantity,
                                                             $thirdOre, $thirdQuantity,
                                                             $fourthOre, $fourthQuantity);
        $totalGoo = number_format($totalGoo, 2, ".", ",");

        $totalWorth = $moonCalc->SpatialMoonsTotalWorth($firstOre, $firstQuantity,
                                                        $secondOre, $secondQuantity,
                                                        $thirdOre, $thirdQuantity,
                                                        $fourthOre, $fourthQuantity);
        $totalWorth = number_format($totalWorth, 2, ".", ",");

        return view('moons.displayTotalWorth')->with(['totalWorth' => $totalWorth, 'totalGoo' => $totalGoo]);
    }
}
