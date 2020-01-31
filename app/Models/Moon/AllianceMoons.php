<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllianceMoons extends Model
{
    // Table Name
    protected $table = 'alliance_moons';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    /**
     * The fillable items for each entry
     * 
     * @var array
     */
    protected $fillable = [
        'Region',
        'System',
        'Planet',
        'Moon',
        'Corporation',
        'FirstOre',
        'FirstQuantity',
        'SecondOre',
        'SecondQuantity',
        'ThirdOre',
        'ThirdQuantity',
        'FourthOre',
        'FourthQuantity',
        'Available',
    ];
}
