<?php

namespace App;

use Laravel\Socialite\Contracts\User as ProviderUser;

class SSOAccountService {

    public function createOrGetUser(ProviderUser $providerUser) {
        $account = SocialAccount::whereProvider('eveonline')->whereProviderUserId($providerUser->getId())->first();
        
        if($account) {
            return $account->user;
        } else {
            $account = new SSOAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'eveonline',
            ]);

            $user = User::whereEmail($providerUser->getCharacterId())->first();


        }
    }
}