<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Observer extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_observers';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'last_updated',
        'observer_id',
        'observer_type',
    ];

    public function getLedgers() {
        return $this->hasMany('App\Models\MiningTax\Ledger', 'observer_id', 'observer_id');
    }

    
}
