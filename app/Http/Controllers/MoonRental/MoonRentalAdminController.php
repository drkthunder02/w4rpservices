<?php

namespace App\Http\Controllers\MoonRental;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

class MoonRentalAdminController extends Controller
{
    //Constructor
    public function __construct() {
        $this->middleware('role:Admin');
        $this->middleware('permission:moon.rental.manager');
    }

    /**
     * Display rental requests
     */
    public function displayRentalRequests() {

    }

    /**
     * Create monthly moon rental
     */
    public function storeRentalRequest() {

    }

    /**
     * Delete / remove monthly moon rental
     */
    public function updateRentalRequest() {

    }

    /**
     * Display current monthly moon rentals
     */
    public function displayCurrentRentals() {

    }
    
}
