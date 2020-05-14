<?php

namespace App\Models\PublicContracts;

use Illuminate\Database\Eloquent\Model;

class PublicContract extends Model
{
    //Table Name
    protected $table = 'public_contracts';

    //Timestamps
    public $timestamps = false;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'buyout',
        'collateral',
        'contract_id',
        'date_expired',
        'date_issued',
        'days_to_complete',
        'end_location_id',
        'for_corporation',
        'issuer_corporation_id',
        'issuer_id',
        'price',
        'reward',
        'start_location_id',
        'title',
        'type',
        'volume',
    ];
}
