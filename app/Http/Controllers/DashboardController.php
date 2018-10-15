<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library;
//use Illuminate\Foundation\Validation\ValidatesRequests;
//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard');
    }

    public function addMoon() {
        return view('dashboard.addmoon');
    }

    public function profile() {
        /**
         * Check to see if the user has a valid esi token
         */
        $user = Auth::user();
        //Try to find the user's ESI token.
        $token = DB::table('esitokens')->where('CharacterId', $user['character_id'])->first();
        if($token != null) {
            $html = '<h3>ESI Token Already Stored</h3>';
        } else {
            //Setup the display button if the user doesn't have an ESI registered
            $state = uniqid();
            session(['state' => $state]);
            $esiLogin = new \App\Library\EsiLogin();
            $html = $esiLogin->DisplayLoginButton($state);
        }

        
        return view('dashboard.profile')->with('html', $html);
    }

    public function callback(Request $request) {
        $esiLogin = new \App\Library\EsiLogin();
        //Pull the old session state from the session, and delete the key
        $oldState = $request->session()->pull('state');
        //Check the state to make sure it matches
        if($oldState == $request->input('state')) {
            $esiLogin->RetrieveAccessToken();
            $esiLogin->RetrieveCharacterId();
            //Store the token in the database
            $token = new \App\EsiToken;
            $token->CharacterId = $esiLogin->GetCharacterId();
            $token->AccessToken = $esiLogin->GetAccessToken();
            $token->RefreshToken = $esiLogin->GetRefreshToken();
            $token->ExpiresIn = $esiLogin->GetRefreskTokenExpiry();
            $token->save();
            //Return view back to profile with success message
            return view('dashboard')->with('message', 'Success!');
        } else {
            //Return view with error message back to the dashboard
            return view('dashboard')->with('message', 'Error!');
        }
        
    }

    public function displayMoons() {
        $moons = DB::table('moons')->get();
        
        return 'Moons Display Table';
    }
}
