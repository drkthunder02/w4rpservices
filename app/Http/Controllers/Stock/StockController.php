<?php

namespace App\Http\Controllers\Stocks;

use Illuminate\Http\Request;

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
