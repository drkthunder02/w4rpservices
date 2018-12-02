<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'permission',
    ];

    protected $table = 'user_permissions';

    /**
     * The attributes that should be hidden for arrays
     * 
     * @var array
     */
    protected $hidden = [];

    protected $guarded = [];
}
