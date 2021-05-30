<?php

namespace App\Models\MoonRental;

use Illuminate\Database\Eloquent\Model;

class AllianceMoonRental extends Model
{
    /**
     * Table Name
     */
    public $table = 'alliance_moon_rentals';

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
        'moon_name',
        'rental_amount',
        'rental_start',
        'rental_end',
        'next_billing_date',
        'entity_id',
        'entity_name',
        'entity_type',
    ];
}
