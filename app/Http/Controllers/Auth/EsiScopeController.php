<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use Socialite;
use Auth;

use App\User;

class EsiScopeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayScopes() {
        //Get the ESI Scopes for the user
        $scopes = DB::table('EsiScopes')->where('character_id', Auth::user()->character_id)->get();
        return view('scopes.select')->with('scopes', $scopes);
    }

    public function redirectToProvider(Request $request) {
        //Redirect to the socialite provider
        return Socialite::driver('eveonline')->setScopes($request->scopes)->redirect();
    }

}
