<?php

namespace App\Http\Controllers\PlanetaryInteraction;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanetaryInteractionController extends Controller
{
    /**
     * Create a new controller instance
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayAlyssaPi() {
        return view('pi.user.display.alyssa');
    }
}
