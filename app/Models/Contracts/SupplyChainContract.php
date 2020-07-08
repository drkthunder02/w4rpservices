<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

class SupplyChainContract extends Model
{
    //Table Name
    public $table = 'supply_chain_contracts';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'issuer_id',
        'issuer_name',
        'title',
        'type',
        'end_date',
        'delivery_by',
        'body',
        'state',
        'final_cost',
    ];

    //Relationship
    public function Bids() {
        return $this->hasMany('App\Models\Contracts\SupplyChainBid', 'contract_id', 'id');
    }


}
