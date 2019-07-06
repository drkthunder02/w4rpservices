<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolarSystemDistance extends Model
{
    //Table Name
    public $table = 'solar_system_distances';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'start_id',
        'start_name',
        'end_id',
        'end_name',
        'distance',
    ];
}
