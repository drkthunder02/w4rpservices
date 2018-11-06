<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('isSuperAdmin', function($user) {
            return $user->hasRole('SuperAdmin');
        });

        $gate->define('isAdmin', function($user) {
            return $user->hasRole('Admin');
        });

        $gate->define('isUser', function($user) {
            return $user->hasRole('User');
        });

        $gate->define('isGuest', function($user) {
            return $user->hasRole('Guest');
        });

        $gate->define('isNone', function($user) {
            return $user->hasRole('None');
        });

        /*
        $gate->define('isSuperAdmin', function($user) {
            return $user->role == 'SuperAdmin';
        });

        $gate->define('isAdmin', function($user) {
            return $user->role == 'Admin';
        });

        $gate->define('isUser', function($user) {
            return $user->role == 'User';
        });

        $gate->define('isGuest', function($user) {
            return $user->role == 'Guest';
        });

        $gate->define('isNone', function($user) {
            return $user->role == 'None';
        });
        */
    }
}
