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
        'sig_id',
        'duration_left',
        'date_scanned',
        'time_scanned',
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
