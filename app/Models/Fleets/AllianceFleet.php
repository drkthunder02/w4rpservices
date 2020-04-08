<?php

namespace App\Models\Fleets;

use Illuminate\Database\Eloquent\Model;

class AllianceFleet extends Model
{
    /**
     * Table Name
     */
    protected $table = 'alliance_fleets';

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
        'fleet_id',
        'fleet_commander_id',
        'fleet_commander_name',
        'member_count',
        'fleet_opened_time',
        'fleet_closed_time',
    ];
}
