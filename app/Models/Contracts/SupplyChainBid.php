<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

class SupplyChainBid extends Model
{
    //Table Name
    public $table = 'supply_chain_bids';

    //Primary Key
    public $primaryKey = 'id';

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
        return $this->belongsTo(App\Models\Contracts\SupplyChainContract::class, 'contract_id', 'contract_id');
    }

    public function Contract() {
        return $this->belongsTo(App\Models\Contracts\SupplyChainContract::class);
    }

    //Model functions
    public function getContractId() {
        return $this->contract_id;
    }
}
