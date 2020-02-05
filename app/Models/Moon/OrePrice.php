<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class OrePrice extends Model
{
    // Table Name
    protected $table = 'ore_prices';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    /**
     * Fillable Array
     * 
     * @var array
     */
    protected $fillable = [
        'Name',
        'ItemId',
        'BatchPrice',
        'UnitPrice',
        'm3Price',
        'Time',
    ];
}
