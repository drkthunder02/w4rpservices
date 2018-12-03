<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;

class FleetActivity extends Model
{
    // Table Name
    protected $table = 'fleet_activity_tracking';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'fleetId',
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
        'region',
        'system',
        'ship',
        'ship_type',
        'created_at',
        'updated_at',
    ];
}
