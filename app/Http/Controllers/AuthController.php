<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AuthAccountService;
use Socialite;
use SocialiteUser;
use App\User;



use DB;

class AuthController extends Controller
{
    //use AuthenticatesUsers;

    /**
     * Redirect the user to the Eve Online authentication page.
     * 
     * @return Response
     */
    public function redirectToProvider() {
        return Socialite::driver('eveonline')->setScopes(['publicData'])->redirect();
    }

    /**
     * Obtain the user information from Eve Online
     * 
     * @return Response
     */
    public function handleProviderCallback(AuthAccountService $service) {
        $ssoUser = Socialite::driver('eveonline')->user();

        $user = $service->createOrGetUser($ssoUser);

        auth()->login($user);

        return redirect()->to('/dashboard');
        dd($user);
    }

    
}
