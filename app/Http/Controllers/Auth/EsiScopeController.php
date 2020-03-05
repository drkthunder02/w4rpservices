<?php

namespace App\Http\Controllers\Auth;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use Auth;

//Models
use App\Models\User\User;
use App\Models\Esi\EsiScope;

class EsiScopeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayScopes() {
        //Get the ESI Scopes for the user
        $scopes = EsiScope::where([
            'character_id' => Auth::user()->character_id,
        ])->get();

        return view('scopes.select')->with('scopes', $scopes);
    }

    public function redirectToProvider(Request $request) {
        //Redirect to the socialite provider
        return Socialite::driver('eveonline')->setScopes($request->scopes)->redirect();
    }

}
