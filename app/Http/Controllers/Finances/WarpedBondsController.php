<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarpedBondsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function DisplayAvailableBonds() {

    }

    public function StoreBonds() {

    }

    public function RedeemBonds() {
        
    }
}
