<?php

namespace App\Http\Controllers;

use Socialite;
use SocialiteUser;
use App\User;
//use Laravel\Socialite\Contracts\Factory as Socialite;
//use Laravel\Socialite\Two\User as SocialiteUser;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    public function handleProviderCallback() {
        $eve_data = Socialite::driver('eveonline')->user();
        //Get or create the User bound to this login
        $user = $this->findOrCreateUser($eve_data);
        //Auth the user
        auth()->login($user);

        return redirect()->to('/dashboard');

        dd($user);
    }

    /**
     * Check if a user exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function findOrCreateUser(SociateUser $eve_user): User {
        //check if the user already exists in the database
        if($existing = User::find($eve_user->character_id)) {
            //Check the owner hash and update if necessary
            if($existing->character_owner_hash !== $eve_user->character_owner_hash) {
                $existing->owner_has = $eve_user->character_owner_hash;
                $existing->save();
            }
            
            return $existing;
        }

        return User::forceCreate([
            'id' => $eve_user->character_id,
            'name' => $eve_user->name,
            'owner_hash' => $eve_user->character_owner_hash,
            'email' => null,
        ]);
    }

    public function loginUser(User $user): bool {
        auth()->login($user, true);

        return true;
    }
}
