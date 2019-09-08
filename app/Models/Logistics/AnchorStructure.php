<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class AnchorStructure extends Model
{
    //Table Name
    public $table = 'alliance_anchor_structure';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'corporation_id',
        'corporation_name',
        'system',
        'structure_size',
        'structure_type',
        'requested_drop_time',
        'requester_id',
        'requester',
    ];
}
