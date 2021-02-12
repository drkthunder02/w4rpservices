<?php

namespace App\Providers\Socialite\EveOnline;

use SocialiteProviders\Managers\SocialiteWasCalled;

/**
 * Class EveOnlineExtendSocialite
 * 
 * @package
 */
class EveOnlineExtendSocialite {
    public function handle(SocialiteWasCalled $socialiteWasCalled) {
        $socialiteWasCalled->extendSocialite('eveonline', Provider::class);
    }
}

?>