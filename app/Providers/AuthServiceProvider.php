<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use DB;
use App\Models\User\UserRole;

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
            //Get the roles the user has from the user_roles table and check against the gate we are creating
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role === 'Admin') {
                //User has the Admin role
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isDirector', function($user) {
            //Get the roles the user has from the user_roles table and check against the gate we are creating
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role === 'Director') {
                //User has the Director role
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isUser', function($user) {
            //Get the roles the user has from the user_roles table and check against the gate we are creating
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role === 'User') {
                //User has the User role
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isGuest', function($user) {
            //Get the roles the user has from the user_roles table and check against the gate we are creating
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role === 'Guest') {
                //User has the Guest role
                return true;
            } else {
                return false;
            }
        });

        $gate->define('isNone', function($user) {
            //Get the roles the user has from the user_roles table and check against the gate we are creating
            $check = DB::table('user_roles')->where('character_id', auth()->user()->character_id)->get(['role']);
            if($check[0]->role === 'None') {
                //User has no role
                return true;
            } else {
                return false;
            }
        });
    }
}
