<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use DB;
use App\Models\User\UserPermission;

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
     * We use gates in some of the graphics, but will work to utilize if statements instead shortly
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('isAdmin', function($user) {
            
            $check = UserPermission::where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]-> role === 'role.admin') {
                return true;
            } else {
                return false;
            }
            
        });

        $gate->define('isDirector', function($user) {
           
            $check = UserPermission::where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]-> role === 'role.director') {
                return true;
            } else {
                return false;
            }
           
        });

        $gate->define('isUser', function($user) {
            
            $check = UserPermission::where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]-> role === 'role.user') {
                return true;
            } else {
                return false;
            }
            
        });

        $gate->define('isGuest', function($user) {
           
            $check = UserPermission::where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]-> role === 'role.guest') {
                return true;
            } else {
                return false;
            }
            
        });

        $gate->define('isNone', function($user) {
            
            $check = UserPermission::where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]-> role === 'role.none') {
                return true;
            } else {
                return false;
            }
        });
    }
}
