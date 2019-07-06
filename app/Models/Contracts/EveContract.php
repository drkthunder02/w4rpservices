<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

class EveContract extends Model
{
    //Table name
    public $table = 'eve_contracts';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'acceptor_id',
        'assignee_id',
        'availability',
        'buyout',
        'collateral',
        'date_accepted',
        'date_completed',
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
        'status',
        'title',
        'volume',
    ];
}
