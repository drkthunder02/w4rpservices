<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Observer extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_observers';

    //Primary Key
    public $primaryKey = 'id';

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
        'observer_name',
    ];    
}
