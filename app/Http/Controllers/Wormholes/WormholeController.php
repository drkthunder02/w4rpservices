<?php

namespace App\Http\Controllers\Wormholes;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

//User Libraries

//Models

class WormholeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayWormholeForm() {

    }

    public function storeWormholeForm() {

    }

    public function displayWormholes() {

    }

    private function wormholes() {
    
    }
}
