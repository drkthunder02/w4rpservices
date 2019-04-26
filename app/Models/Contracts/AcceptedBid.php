<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

class AcceptedBid extends Model
{
    // Table Name
    public $table = 'accepted_bid';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'bid_id',
        'amount',
    ];
}
