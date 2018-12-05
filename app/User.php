<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

use App\Models\User\UserRole;
use App\Models\User\UserPermission;

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

    public function getUserType() {
        return User::where('user_type')->get();
    }

    public function role() {
        return $this->hasOne('App\Models\UserRole', 'character_id');
    }

    public function permissions() {
        return $this->hasMany('App\Models\UserPermission', 'character_id');
    }

    public function esitoken() {
        return $this->hasOne('App\Models\EsiToken', 'character_id', 'character_id');
    }

    public function hasPermission($permission) {
        $found = UserPermission::where(['character_id' => $this->character_id, 'permission' => $permission])->get(['permission']);
        dd($found);
        $found = DB::table('user_permissions')->where(['character_id' => $this->character_id, 'permission' => $permission])->get(['permission']);
        if($found[0]->permission == $permission) {
            return true;
        } else {
            return false;
        }
    }

    public function hasRole($role, $permission = true) {
        //If the user is a super user then he has all roles
        if($this->hasSuperUser()) {
            return true;
        }

        if(UserRole::where(['character_id' => $this->character_id, 'role' => $role])->get()) {
            return true;
        } else {
            return false;
        }
    }

    public function hasSuperUser() {
        //Search for the super user role for the character from the database
        $found = DB::table('user_roles')->where(['character_id' => $this->character_id, 'role' => 'SuperUser'])->get(['role']);
        //If we find the SuperUser role, then the user has it, and returns true, else returns false
        if($found == 'SuperUser') {
            return true;
        } else {
            return false;
        }
    }
}
