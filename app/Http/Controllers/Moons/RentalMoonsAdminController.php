<?php

namespace App\Http\Controllers\Moons;

//Internal Library
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Log;
use Carbon\Carbon;

//Models
use App\Models\Moon\AllianceRentalMoon;

class RentalMoonsAdminController extends Controller
{
    /**
     * Construct
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middelware('role:user');
        $this->middleware('permission:mining.director');
    }

    /**
     * Function to display rental moons being used for the alliance
     */
    public function displayAllianceUsageRentalMoons() {
        
    }


    /**
     * Display the form for requesting new rental moon for the alliance
     */
    public function displayRentalMoonForAllianceForm() {

    }
    

    /**
     * Function to store when a new rental moon is requested
     */
    public function storeRentalMoonForAlliance(Request $request) {

    }

    /**
     * Function to display the form for figuring out item composition
     */
    public function displayItemCompositionForm() {

    }

    /**
     * Function to display the results of the form for figuring out item composition
     */
    public function displayItemCompositionResults(Request $request) {

    }
}
