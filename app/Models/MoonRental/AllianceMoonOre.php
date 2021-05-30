<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllianceMoonOre extends Model
{
    /**
     * Table Name
     */
    public $table = 'alliance_moon_ores';

    /**
     * Primary Key
     */
    public $primaryKey = 'id';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'moon_id',
        'moon_name',
        'ore_type_id',
        'ore_name',
    ];
}
