<?php

namespace App\Http\Controllers\Stocks;

use Illuminate\Http\Request;

/**
 * This class display data related to structures and how much fuel of a given type they hold.
 * This will mostly be used for jump gates, cyno beacons, and cyno jammers.
 */
class StockController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayStock() {

    }

    public function displayStation($stationId) {

    }

    public function displayStockForm() {

    }

    public function processStockForm(Request $request) {
        
    }
}
