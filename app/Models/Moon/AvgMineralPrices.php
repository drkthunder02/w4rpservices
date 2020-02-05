<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class AvgMineralPrices extends Model
{
    //Table Name
    protected $table = 'avg_mineral_prices';

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
        'Price',
        'Time',
    ];
}
