<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

class SupplyChainBid extends Model
{
    //Table Name
    public $table = 'supply_chain_bids';

    // Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'bid_amount',
        'entity_id',
        'entity_name',
        'entity_type',
        'bid_type',
        'bid_note',
    ];

    //Relationships
    public function ContractId() {
        return $this->hasOne('App\Models\Contracts\SupplyChainContract', 'contract_id', 'contract_id');
    }

    public function Contract() {
        return $this->belongsTo(SupplyChainContract::class);
    }

    //Model functions
    public function getContractId() {
        return $this->contract_id;
    }
}
