<?php

namespace App\Models\Lookups;

use Illuminate\Database\Eloquent\Model;

class SolarSystem extends Model
{
    //Table Name
    public $table = 'solar_systems';

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
        'name',
        'solar_system_id',
    ];
}
