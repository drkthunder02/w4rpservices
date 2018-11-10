<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use DB;
use \App\Models\UserRole;

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
            if($check[0]->role == 'Admin') {
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isUser', function($user) {
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role == 'User') {
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isGuest', function($user) {
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role == 'Guest') {
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isNone', function($user) {
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role == 'None') {
                return true;
            } else {
                return false;
            }
        });
    }
}
