<?php

namespace App;

use Laravel\Socialite\Contracts\User as ProviderUser;

class AuthAccountService {
    /**
     * Check if a user exists in the database, else, create and 
     * return the user object.
     * 
     * @param \Laravel\Socialite\Two\User $user
     */
    private function createOrGetUser(Provider $eve_user) {

        $account = AuthAccount::whereProvider('eveonline')->whereProviderUserId($providerUser->getId())->first();

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
/*
        //check if the user already exists in the database
        if($existing = User::find($eve_user->character_id)) {
            //Check the owner hash and update if necessary
            if($existing->character_owner_hash !== $eve_user->character_owner_hash) {
                $existing->owner_has = $eve_user->character_owner_hash;
                $existing->save();
            }
            
            return $existing;
        }

        if(!eve_user)

        return User::forceCreate([
            'id' => $eve_user->character_id,
            'name' => $eve_user->name,
            'owner_hash' => $eve_user->character_owner_hash,
            'email' => null,
        ]);
        */
    }
    
}