<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_ledgers';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'last_updated',
        'type_id',
        'ore_name',
        'quantity',
        'amount',
        'invoiced',
    ];
}
