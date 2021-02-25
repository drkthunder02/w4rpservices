<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_ledgers';

    //Timestamps
    public $timestamps = false;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'character_name',
        'observer_id',
        'last_updated',
        'type_id',
        'ore_name',
        'quantity',
        'amount',
        'invoiced',
        'invoice_id',
        'created_at',
        'updated_at',
    ];

    public function getInvoice() {
        return $this->belongsTo('App\Models\MiningTax\Invoice', 'invoice_id', 'invoice_id');
    }

    
}
