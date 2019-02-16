<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class MoonRent extends Model
{
    // Table Name
    protected $table = 'moon_rents';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    //Fillable Items
    protected $fillable = [
        'System',
        'Planet',
        'Moon',
        'RentalCorp',
        'RentalEnd',
        'Contact',
        'Price',
    ];
}
