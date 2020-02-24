<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class AllianceMoon extends Model
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
