<?php

namespace App\Models\Lookups;

use Illuminate\Database\Eloquent\Model;

class CorporationToAlliance extends Model
{
    //Table Name
    public $table = 'corporation_to_alliance';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'corporation_id',
        'corporation_name',
        'alliance_id',
        'alliance_name',
    ];
}
