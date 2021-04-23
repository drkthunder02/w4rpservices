<?php

namespace App\Http\Controllers\MoonRental;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

class MoonRentalController extends Controller
{
    //Constructor
    public function __construct() {
        $this->middleware('role:user');
    }

    /**
     * Display all of the available moons for rental
     */
    public function displayMoons() {

    }

    /**
     * Display form to request new moon structure be placed
     */
    public function displayNewMoonRequestForm() {

    }

    /**
     * Display moon rental request form
     */
    public function displayMoonRentalRequestForm() {

    }

    /**
     * Store moon rental from the request form
     */
    public function storeMoonRentalRequest() {

    }
}
