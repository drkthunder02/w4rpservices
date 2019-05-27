<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    /**
     * Requires the scope:
     * esi-universe.read_structures.v1
     */

    //Table Name
    public $table = '';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'owner_id',
        'position_x',
        'position_y',
        'position_z',
        'solar_system_id',
        'type_id',
    ];
}
