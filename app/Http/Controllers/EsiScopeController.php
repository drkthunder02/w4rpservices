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
        //Get the ESI Scopes for the user
        $scopes = DB::table('EsiScopes')->where('character_id', Auth::user()->character_id)->get();
        //We want to send the scopes to the page as pre-checked.
        dd($scopes);
        return view('scopes.select')->with('scopes', $scopes);
    }

    public function redirectToProvider(Request $request) {
        //Redirect to the socialite provider
        return Socialite::driver('eveonline')->setScopes($request->scopes)->redirect();
    }

}
