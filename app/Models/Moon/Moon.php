<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class Moon extends Model
{
    // Table Name
    protected $table = 'Moons';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    protected $fillable = [
        'Region',
        'System',
        'Planet',
        'Moon',
        'StructureName',
        'FirstOre',
        'FirstQuantity',
        'SecondOre',
        'SecondQuantity',
        'ThirdOre',
        'ThirdQuantity',
        'FourthOre',
        'FourthQuantity',
        'RentalCorp',
        'RentalEnd',
    ];
}
