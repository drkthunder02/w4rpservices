<?php

namespace App\Models\Character;

use Illuminate\Database\Eloquent\Model;

class BlacklistUser extends Model
{
    //Table Name
    public $table = 'blacklisted_characters';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'name',
        'reason',
        'alts',
        'lister_id',
        'lister_name',
    ];
}
