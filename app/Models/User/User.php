<?php

namespace App\Models\User;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\User\UserRole;
use App\Models\User\UserPermission;
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\MoonRentals\AllianceRentalMoon;
use App\Models\SRP\SRPShip;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Database Table
     */
    protected $table = 'users';

    //Primary Key
    public $primaryKey = 'id';

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
        'user_type',
    ];

    

    public function role() {
        return $this->hasOne('\App\Models\User\UserRole', 'character_id', 'character_id');
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

    public function userAlts() {
        return $this->hasMany('App\Models\User\UserAlt', 'character_id', 'main_id');
    }

    public function altCount() {
        return UserAlt::where(['main_id' => $this->character_id])->count();
    }

    public function getAlts() {
        return UserAlt::where(['main_id' => $this->character_id])->get();
    }

    public function hasPermission($permission) {
        $found = UserPermission::where(['character_id' => $this->character_id, 'permission' => $permission])->get(['permission']);
        foreach($found as $foo) {
            if($foo->permission === $permission) {
                return true;
            }
        }

        return false;
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

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->character_id;
    }

    public function getUserType() {
        return $this->user_type;
    }

    public function getRole() {
        $role = UserRole::where(['character_id' => $this->character_id])->first();

        return $role->role;
    }

    public function srpOpen() {
        return SRPShip::where([
            'character_id' => $this->character_id,
            'approved' => 'Under Review',
        ])->count();
    }

    public function srpDenied() {
        return SRPShip::where([
            'character_id' => $this->character_id,
            'approved' => 'Denied',
        ])->count();
    }

    public function srpApproved() {
        return SRPShip::where([
            'character_id' => $this->character_id,
            'approved' => 'Approved',
        ])->count();
    }
}
