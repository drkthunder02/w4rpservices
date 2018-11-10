<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use DB;

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
     * These gates will always choose the highest roles
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('isAdmin', function($user) {
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            dd($check);
            if($check == 'Admin') {
                return true;
            }
            //return $user->hasRole('Admin');
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
    }
}
