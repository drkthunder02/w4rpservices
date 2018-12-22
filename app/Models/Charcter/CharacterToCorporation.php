<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterToCorporation extends Model
{
    // Table Name
    public $table = 'character_to_corporation';

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
