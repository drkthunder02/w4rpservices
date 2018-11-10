<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Socialite;
use Auth;
use App\User;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class EsiScopeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayScopes() {
        return view('scopes.select');
    }

    public function redirectToProvider(Request $request) {
        dd($request);

        return Socialite::driver('eveonline')->setScopes($request->scopes)->redirect();
    }

}
