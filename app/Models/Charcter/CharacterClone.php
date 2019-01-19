<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class CharacterClone extends Model
{
    // Table Name
    public $table = 'clones';

    // Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'active',
    ];
}
