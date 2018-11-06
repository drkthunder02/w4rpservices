<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;



class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'avatar', 
        'owner_hash', 
        'character_id',
        'inserted_at',
        'expires_in', 
        'access_token', 
        'refresh_token', 
        'user_type',
        'scopes',
        'role',
    ];

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guarded = [];

    //Used in middleware to make sure a user is able to access many of the pages
    public function hasRole($role)
    {
        $checks = User::roles()->get();
        //$charId = User::where('character_id')->get();
        //$checks = DB::table('user_roles')->where('character_id', $charId)->get();
        foreach($checks as $check) {
            if($check['role'] == $role) {
                return true;
            }
        }

        return false;
        //return User::where('role', $role)->get();
    }

    public function getUserType() {
        return User::where('user_type')->get();
    }

    public function roles() {
        return $this->hasMany('App\Models\UserRole');
    }

    public function esitoken() {
        return $this->hasOne('App\Models\EsiToken');
    }
}
