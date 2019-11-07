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
use App\Models\Moon\Moon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;
use App\Models\MoonRent\MoonRental;

//Library
use App\Library\Moons\MoonCalc;

class MoonsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:Renter');
    }

    /**
     * Function to display the moons and pass data to the blade template
     */
    public function displayMoons() {
        $rentalEnd = '';

        //Get the user type from the user Auth class
        $type = Auth::user()->user_type;
        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        //get all of the moons from the database
        $moons = DB::table('Moons')->orderBy('System', 'asc')->get();
        //Set the rental date as last month for moons not rented
        $lastMonth = Carbon::now()->subMonth();
        //Set a variable for today's date
        $today = Carbon::now();

        //declare the html variable and set it to null
       
        $table = array();
        $moonprice = null;
        foreach($moons as $moon) {
            //get the rental data for the moon
            $rental = MoonRental::where([
                'System' => $moon->System,
                'Planet' => $moon->Planet,
                'Moon' => $moon->Moon,
            ])->first();

            if($rental == false) {
                //If we don't find a rental record, set the rental date as last month
                $rentalTemp = $lastMonth;
                $rentalEnd = $rentalTemp->format('m-d');
            } else {
                //Set the rental date up
                $rentalTemp = new Carbon($rental->RentalEnd);
                $rentalEnd = $rentalTemp->format('m-d');
            }

            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                    $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

            $worth = $moonCalc->SpatialMoonsTotalWorth($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                       $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

            if($type == 'W4RP') {
                $moonprice = $price['alliance'];
            } else {
                $moonprice = $price['outofalliance'];
            }

            if($rentalTemp->diffInDays($today) < 3 ) {
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
                'RowColor' => $color,
            ]);
        }

        return view('moons.user.moon')->with('table', $table);
    }

    public function displayTotalWorthForm() {
        return view('moons.user.formTotalWorth');
    }

    public function displayTotalWorth(Request $request) {
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

        //Setup calls to the MoonCalc class
        $moonCalc = new MoonCalc();
        $composition = array();
        $totalPull = 5.55 * (3600.00 * 24.00 * 30.00);

        //Calculate the total moon goo value
        $totalGoo = $moonCalc->SpatialMoonsOnlyGooTotalWorth($firstOre, $firstQuantity, $secondOre, $secondQuantity,
                                                             $thirdOre, $thirdQuantity, $fourthOre, $fourthQuantity);
        //Format the number to send to the blade
        $totalGoo = number_format($totalGoo, 2, ".", ",");

        //Calculate the total worth of the moon
        $totalWorth = $moonCalc->SpatialMoonsTotalWorth($firstOre, $firstQuantity, $secondOre, $secondQuantity,
                                                        $thirdOre, $thirdQuantity, $fourthOre, $fourthQuantity);
        //Format the number to send to the blade.
        $totalWorth = number_format($totalWorth, 2, ".", ",");

        //Get the composition for the first ore if it is not None.
        //Add the first ore composition to the final composition
        if($firstOre != 'None') {
            $firstComp = $moonCalc->GetOreComposition($firstOre);
            $rUnits = $moonCalc->CalcReprocessingUnits($firstOre, $firstQuantity);
            
            dd($firstComp);

            foreach($firstComp as $key => $value) {
                $composition[$key] += floor(($firstComp[$key] * $rUnits) * $reprocessing);
            }
        }

        //Get the composition for the second ore if it is not None.
        //Add the second ore composition to the final composition
        if($secondOre != 'None') {
            $secondComp = $moonCalc->GetOreComposition($secondOre);
            $rUnits = $moonCalc->CalcReprocessingUnits($secondOre, $secondQuantity);

            foreach($secondComp as $key => $value) {
                $composition[$key] += floor(($secondComp[$key] * $rUnits) * $reprocessing);
            }
        }

        //Get the composition for the third ore if it is not None
        //Add the third ore composition to the final composition
        if($thirdOre != 'None') {
            $thirdComp = $moonCalc->GetOreComposition($thirdOre);
            $rUnits = $moonCalc->CalcReprocessingUnits($thirdOre, $thirdQuantity);

            foreach($thirdComp as $key => $value) {
                $composition[$key] += floor(($thirdComp[$key] * $rUnits) * $reprocessing);
            }
        }

        //Get the composition for the fourth ore if it is not None
        //Add the fourth ore composition to the final composition
        if($fourthOre != 'None') {
            $fourthComp = $moonCalc->GetOreComposition($fourthOre);
            $rUnits = $moonCalc->CalcReprocessingUnits($fourthOre, $fourthQuantity);

            foreach($fourthComp as $key => $value) {
                $composition[$key] += floor(($fourthComp[$key] * $rUnits) * $reprocessing);
            }
        }

        //Remove any items which don't equal a number above 0 in the composition in order to remove them from the total.
        //The less we display on the table the better.
        foreach($composition as $key => $value) {
            if($composition[$key] === 0) {
                unset($composition[$key]);
            }
        }


        return view('moons.user.displayTotalWorth')->with('totalWorth', $totalWorth)
                                                   ->with('totalGoo', $totalGoo)
                                                   ->with('composition', $composition);
    }
}
