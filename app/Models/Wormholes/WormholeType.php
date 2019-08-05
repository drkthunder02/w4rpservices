<?php

namespace App\Models\Wormholes;

use Illuminate\Database\Eloquent\Model;

class WormholeType extends Model
{
    //Table Name
    public $table = 'wormhole_types';

    //Timestamps
    public $timestamps = false;

    //Primary Key
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'type',
        'leads_to',
        'mass_allowed',
        'individual_mass',
        'regeneration',
        'max_stable_time',
    ];
}
