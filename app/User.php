<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

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
        return $this->hasOne('App\Models\User\UserRole', 'character_id');
    }

    public function permissions() {
        return $this->hasMany('App\Models\User\UserPermission', 'character_id');
    }

    public function esitoken() {
        return $this->hasOne('App\Models\Esi\EsiToken', 'character_id', 'character_id');
    }

    public function esiScopes() {
        return $this->hasMany('App\Models\Esi\EsiScope', 'character_id');
    }

    public function hasPermission($permission) {
        $found = UserPermission::where(['character_id' => $this->character_id, 'permission' => $permission])->get(['permission']);
        if(isset($found[0]->permission) && $found[0]->permission == $permission) {
            return true;
        } else {
            return false;
        }
    }

    public function tickets() {
        return $this->hasMany('App\Models\HelpDesk\HelpDeskTicket', 'character_id');
    }

    public function hasEsiScope($scope) {
        $found = EsiScope::where(['character_id' => $this->character_id, 'scope' => $scope])->get(['scope']);
        if(isset($found[0]->scope) && $found[0]->scope == $scope) {
            return true;
        } else {
            return false;
        }
    }

    public function hasRole($role) {
        //If the user is a super user then he has all roles
        if($this->hasSuperUser()) {
            return true;
        }

        $found = UserRole::where(['character_id' => $this->character_id, 'role' => $role])->get(['role']);

        if(isset($found[0]) && $found[0]->role == $role) {
            return true;
        } else {
            return false;
        }
    }

    public function hasSuperUser() {
        //Search for the super user role for the character from the database
        $found = UserRole::where(['character_id' => $this->character_id, 'role' => 'SuperUser'])->get(['role']);
        //If we find the SuperUser role, then the user has it, and returns true, else returns false
        if(isset($found[0]->role) && $found[0]->role == 'SuperUser') {
            return true;
        } else {
            return false;
        }
    }
}
