<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_payments';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'character_name',
        'invoice_id',
        'invoice_amount',
        'payment_amount',
        'payment_date',
        'status',
    ];
}
