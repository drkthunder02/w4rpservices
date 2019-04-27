<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    // Table Name
    public $table = 'contract_bids';

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
        'character_name',
        'character_id',
        'corporation_name',
        'corporation_id',
    ];

    protected $guarded = [];

    public function ContractId() {
        return $this->hasOne('App\Models\Contracts\Contract', 'id', 'contract_id');
    }

    public function Contract() {
        return $this->belongsTo(Contract::class);
    }
}
