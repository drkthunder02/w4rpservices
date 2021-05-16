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
    public $timestamps = true;

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
        'type_name',
        'corporation_id',
        'services',             //True or false on whether it has services which are held in a different table
        'state',
        'state_timer_start',
        'state_timer_end',
        'fuel_expires',
        'profile_id',
        'next_reinforce_apply',
        'next_reinforce_hour',
        'reinforce_hour',
        'unanchors_at',
        'created_at',
        'updated_at',
    ];

    public function services() {
        return $this->hasMany(App\Models\Structure\Service::class, 'structure_id', 'structure_id');
    }

    public function assets() {
        return $this->hasMany(App\Models\Structure\Asset::class, 'location_id', 'structure_id');
    }
}
