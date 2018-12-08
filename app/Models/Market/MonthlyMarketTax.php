<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MonthlyMarketTax extends Model
{
    // Table Name
    protected $table = 'monthly_market_taxes';

    // Timestamps
    public $timestamps = true;

    //Primary Key
    public $primaryKey = 'id';

    protected $fillable = [
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
        'tax_owed',
        'month',
        'year',
    ];
}
