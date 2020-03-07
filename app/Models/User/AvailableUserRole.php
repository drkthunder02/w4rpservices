<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class AvailableUserRole extends Model
{
    //Table Name
    protected $table = 'available_user_roles';

    //Timestamps
    public $timestamps = false;

    /**
     * The attribute that are mass assignable
     */
    protected $fillable = [
        'role',
        'rank',
        'description',
    ];
}
