<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Auth;
use App\Models\UserRole;

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
        //Get the roles from the user_roles table
        $check = DB::table('user_roles')->where(['character_id' => auth()->user()->character_id])->get();
        if($check->role == $role) {
            return true;
        }
        dd($checks);
        foreach($checks as $check) {
            if($check->role == $role) {
                return true;
            }
        }

        return false;
    }

    public function getUserType() {
        return User::where('user_type')->get();
    }

    public function roles() {
        return $this->hasMany('App\Models\UserRole', 'character_id', 'character_id');
    }

    public function esitoken() {
        return $this->hasOne('App\Models\EsiToken', 'character_id', 'character_id');
    }
}
