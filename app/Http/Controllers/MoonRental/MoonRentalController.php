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
        $moons = AllianceMoon::where([
            'rented' => 'No',
        ])->get();

        return view('moon.rental.available.display')->with('moons', $moons);
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

    /**
     * Request a mail job be added to the mail queue to resend mining bill instantly
     */
    public function requestMoonRentalBill() {

    }
}
