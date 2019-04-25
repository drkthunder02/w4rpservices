<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    // Table Name
    public $table = 'contract_bids';

    // Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'bid',
        'accepted',
    ];

    protected $guarded = [];

    public function ContractId() {
        return $this->hasOne('App\Models\Contracts\Contract', 'id', 'contract_id');
    }

    public function Contract() {
        return $this->belongsTo(Contract::class);
    }
}
