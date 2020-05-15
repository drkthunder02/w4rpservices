<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    //Table Name
    protected $table = 'market_prices';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'type_id',
        'adjusted_price',
        'average_price',
        'lowest_price',
        'highest_price',
        'order_count',
    ];
}
