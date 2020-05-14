<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketRegionOrder extends Model
{
    //Table Name
    protected $table = 'market_region_orders';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'duration',
        'is_buy_order',
        'issued',
        'location_id',
        'min_volume',
        'order_id',
        'price',
        'range',
        'system_id',
        'type_id',
        'volume_remain',
        'volume_total',
    ];
}
