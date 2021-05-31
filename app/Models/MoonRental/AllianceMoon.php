<?php

namespace App\Models\MoonRental;

use Illuminate\Database\Eloquent\Model;

class AllianceMoon extends Model
{
    /**
     * Table Name
     */
    public $table = 'alliance_moons';

    /**
     * Primary Key
     */
    public $primaryKey = 'id';

    /**
     * Timestamps
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'moon_id',
        'name',
        'system_id',
        'system_name',
        'moon_type',
        'worth_amount',
        'rented',
        'rental_amount',
    ];
}
