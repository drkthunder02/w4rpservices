<?php

namespace App\Models\Lookups;

use Illuminate\Database\Eloquent\Model;

class MoonLookup extends Model
{
    /**
     * Table Name
     */
    public $table = 'moon_lookup';

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
        'name',
        'position_x',
        'position_y',
        'position_z',
        'system_id',
    ];
}
