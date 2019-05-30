<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    /**
     * Requires the scope:
     * esi-universe.read_structures.v1
     */

    //Table Name
    public $table = 'alliance_structures';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'structure_id',
        'structure_name',
        'solar_system_id',
        'solar_system_name',
        'type_id',
        'corporation_id',
        'services',             //True or false on whether it has services which are held in a different table
        'state',
        'state_timer_start',
        'state_timer_end',
        'fuel_expires',
        'profile_id',
        'position_x',
        'position_y',
        'position_z',
        'next_reinforce_apply',
        'next_reinforce_hour',
        'next_reinforce_weekday',
        'reinforce_hour',
        'reinforce_weekday',
        'unanchors_at',
    ];
}
