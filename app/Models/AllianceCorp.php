<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllianceCorp extends Model
{
    /**
     * Table Name
     */
    protected $table = 'AllianceCorps';

    /**
     * Timestamps
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'corporation_id',
        'name',
    ];
}
