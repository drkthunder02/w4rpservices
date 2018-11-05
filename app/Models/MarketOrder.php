<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketOrder extends Model
{
    // Table Name
    protected $table = 'MarketOrders';

    // Timestamps
    public $timestamps = false;

    // Primary Key
    public $primaryKey = 'order_id';
}
