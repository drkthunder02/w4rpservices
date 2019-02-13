<?php

namespace App\Models\Lookups;

use Illuminate\Database\Eloquent\Model;

class UserToCorporation extends Model
{
    // Table Name
    public $table = 'user_to_corporation';

    // Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
    ];
}
