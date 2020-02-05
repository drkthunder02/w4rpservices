<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class AvgOrePrices extends Model
{
    //Table Name
    protected $table = 'avg_ore_prices';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * Fillable array
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
