<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class LogisticContract extends Model
{
    //Table Name
    public $table = 'logistics_contracts';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'accepted',
        'accepted_by_id',
        'accepted_by_name',
        'status',   
    ];
}
