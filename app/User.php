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

    public function hasRole($role)
    {
        //return User::where('role', $role)->get();
    }

    public function getUserType() {
        return User::where('user_type')->get();
    }

    public function roles() {
        return $this->hasMany('App\UserRole');
    }

    public function esiscopes() {
        return $this->hasMany('App\EsiScope');
    }

    public function esitoken() {
        return $this->hasOne('App\EsiToken');
    }
}
