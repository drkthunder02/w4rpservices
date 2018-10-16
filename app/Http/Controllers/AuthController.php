<?php

namespace App\Http\Controllers;

use Socialite;
use SocialiteUser;
use App\User;
use App\AuthAccountService;
use App\Http\Requests;
use App\Http\Controllers\Controller;

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

        $user = $service->createOrGetUser(Socialite::driver('eveonline')->user());

        auth()->login($user);

        return redirect()->to('/dashboard');
/*
        $eve_data = Socialite::driver('eveonline')->user();

        //Get or create the User bound to this login
        $user = $this->createOrGetUser($eve_data);
        //Auth the user
        auth()->login($user);

        
*/
        dd($user);
    }
/*
    public function loginUser(User $user): bool {
        auth()->login($user, true);

        return true;
    }
*/
}
