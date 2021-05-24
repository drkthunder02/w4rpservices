<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserAlt extends Model
{
    //Table Name
    public $table = 'user_alts';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'main_id',
        'character_id',
        'avatar',
        'access_token',
        'inserted_at',
        'expires_in',
        'owner_hash',
    ];

    public function mainCharacter() {
        return $this->belongsTo('App\Models\User\User', 'character_id', 'main_id');
    }

    public function getMain() {
        return User::where(['character_id' => $this->main_id])->get();
    }
}
