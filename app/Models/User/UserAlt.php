<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserAlt extends Model
{
    //Table Name
    public $table = 'user_alts';

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
        'refresh_token',
        'inserted_at',
        'expires_in',
        'owner_has',
    ];
}
