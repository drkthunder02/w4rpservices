<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    // Table Name
    protected $table = 'Config';

    // Timestamps
    public $timestamps = false;

    /**
     * Fillable Array
     * 
     * @var array
     */
    protected $fillable = [
        'RentalTax',
        'AllyRentalTax',
        'RefineRate',
        'RentalTime',
    ];
}
