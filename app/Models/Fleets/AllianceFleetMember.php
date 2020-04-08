<?php

namespace App\Models\Fleets;

use Illuminate\Database\Eloquent\Model;

class AllianceFleetMember extends Model
{
    /**
     * Table Name
     */
    protected $table = 'alliance_fleet_members';

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
        'character_id',
        'character_name',
        'fleet_joined_time',
        'fleet_leaved_time',
    ];
}
