<?php

namespace App\Models\Wormholes;

use Illuminate\Database\Eloquent\Model;

class AllianceWormhole extends Model
{
    //Table Name
    public $table = 'alliance_wormholes';

    //Timestamps
    public $timestamps = true;

    //Primary Key
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'system',
        'sig_id',
        'duration_left',
        'dateTime',
        'class',
        'type',
        'hole_size',
        'stability',
        'details',
        'link',
        'mass_allowed',
        'individual_mass',
        'regeneration',
        'max_stable_time',
    ];
}
