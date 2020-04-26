<?php

namespace App\Models\MoonRent;

use Illuminate\Database\Eloquent\Model;

class MoonRental extends Model
{
    // Table Name
    protected $table = 'moon_rents';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    //Fillable Items
    protected $fillable = [
        'System',
        'Planet',
        'Moon',
        'StructureId',
        'RentalCorp',
        'RentalEnd',
        'Contact',
        'Type',
        'Price',
        'Paid',
        'Paid_Until',
    ];
}
