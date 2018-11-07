<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FleetsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayRegisterFleet() {
        return view('fleets.registerfleet');
    }

    public function displayStandingFleet() {
        return view('fleets.displaystanding');
    }

    public function registerFleet() {

    }

    public function createWing() {

    }

    public function createSquad()  {

    }

    public function addPilot() {

    }
}
