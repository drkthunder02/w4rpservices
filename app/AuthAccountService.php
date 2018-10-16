<?php

namespace App;

//use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socailiate\Two\User as ProviderUser;

class AuthAccountService {
    /**
     * Check if a user exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    public function createOrGetUser(ProviderUser $eve_user) {
        //Search for user in the database
        

        $account = AuthAccount::whereProvider('eveonline')->whereProviderUserId($eve_user->getId())->first();

        if($account) {
            return $account->user;
        } else {
            $account = new AuthAccount([
                'name' => $user->getName(),
                'email' => null,
                'avatar' => $user->avatar,
                'owner_hash' => $user->character_owner_hash,
                'id'=> $user->getId(),
                'expiresIn' => $user->expiresIn,
                'token' => $user->token,
                'refreshToken' => $user->refreshToken,
            ]);

            $user = User::whereName($providerUser->getName())->first();

            if(!$user) {
                $user = User::create([
                    'name' => $user->getName(),
                    'email' => null,
                    'avatar' => $user->avatar,
                    'owner_hash' => $user->character_owner_hash,
                    'id'=> $user->getId(),
                    'expiresIn' => $user->expiresIn,
                    'token' => $user->token,
                    'refreshToken' => $user->refreshToken,
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            return $user;

        }

    }
    
}