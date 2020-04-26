<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class RentalMoon extends Model
{
    // Table Name
    protected $table = 'RentalMoons';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    protected $fillable = [
        'Region',
        'System',
        'Planet',
        'Moon',
        'StructureId',
        'StructureName',
        'FirstOre',
        'FirstQuantity',
        'SecondOre',
        'SecondQuantity',
        'ThirdOre',
        'ThirdQuantity',
        'FourthOre',
        'FourthQuantity',
    ];
}
