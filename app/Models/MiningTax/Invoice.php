<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_invoices';

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
        'date_issued',
        'date_due',
        'status',
    ];
}
