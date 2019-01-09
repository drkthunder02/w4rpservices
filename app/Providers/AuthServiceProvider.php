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
            $permission = false;
            $perms = UserPermission::where('character_id', auth()->user()->character_id)->get(['permission']);
            foreach($perms as $perm) {
                if($perm === 'role.admin') {
                    $permission = true;
                }
            }
            
            return $permission;
        });

        $gate->define('isDirector', function($user) {
            $permission = false;
            $perms = UserPermission::where('character_id', auth()->user()->character_id)->get(['permission']);
            foreach($perms as $perm) {
                if($perm === 'role.director') {
                    $permission = true;
                }
            }
            
            return $permission;           
        });

        $gate->define('isUser', function($user) {
            $permission = false;
            $perms = UserPermission::where('character_id', auth()->user()->character_id)->get(['permission']);
            foreach($perms as $perm) {
                if($perm === 'role.user') {
                    $permission = true;
                }
            }
            
            return $permission;            
        });

        $gate->define('isGuest', function($user) {
            $permission = false;
            $perms = UserPermission::where('character_id', auth()->user()->character_id)->get(['permission']);
            foreach($perms as $perm) {
                if($perm === 'role.guest') {
                    $permission = true;
                }
            }
            
            return $permission;
        });

        $gate->define('isNone', function($user) {
            $permission = false;
            $perms = UserPermission::where('character_id', auth()->user()->character_id)->get(['permission']);
            foreach($perms as $perm) {
                if($perm === 'role.none') {
                    $permission = true;
                }
            }
            
            return $permission;
        });
    }
}
