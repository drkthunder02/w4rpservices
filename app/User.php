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
        $check = User::role()->get();
        //dd($check);
        if($check['role'] == $role) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserType() {
        return User::where('user_type')->get();
    }

    public function role() {
        return $this->hasOne('App\Models\UserRole', 'character_id');
    }

    public function esitoken() {
        return $this->hasOne('App\Models\EsiToken', 'character_id', 'character_id');
    }
}
