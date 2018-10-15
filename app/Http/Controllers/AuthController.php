<?php

namespace App\Http\Controllers;

//use Laravel\Socialite\Contracts\Factory as Socialite;
//use Laravel\Socialite\Two\User as SocialiteUser;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
//use SocialiteUser;


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
    public function handleProviderCallback(Socialite $social) {
        $user = $social->driver('eveonline')->user();
        Auth::login($user);
        dd($user);
    }
}
