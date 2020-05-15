<?php

namespace App\Models\PublicContracts;

use Illuminate\Database\Eloquent\Model;

class PublicContractItem extends Model
{
    //Table Name
    protected $table = 'public_contract_items';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'is_blueprint_copy',
        'is_included',
        'item_id',
        'material_efficency',
        'quantity',
        'record_id',
        'runs',
        'time_efficiency',
        'type_id',
    ];

    public function contract() {
        return $this->hasOne('App\Models\PublicContracts\PublicContract', 'contract_id', 'contract_id');
    }
}
