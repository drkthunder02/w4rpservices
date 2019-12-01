<?php

namespace App\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class BlacklistUser extends Model
{
    //Table Name
    public $table = 'alliance_blacklist';

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
