<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EveOnlineServiceProvider2 extends ServiceProvider {
    /**
     * Bootstrap any application services
     * 
     * @return void
     */
    public function boot() {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'eveonline',
            function ($app) use ($socialite) {
                $config = $app['config']['services.eveonline'];
                
                return $socialite->buildProvider(EveOnlineSocialiteProvider::class, $config);
            }
        );
    }
}

?>