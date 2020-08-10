<?php

namespace App\Models\Rentals;

use Illuminate\Database\Eloquent\Model;

class RentalSystem extends Model
{
    /**
     * Table Name
     */
    public $table = 'alliance_rental_systems';

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
        'contact_id',
        'contact_name',
        'corporation_id',
        'corporation_name',
        'system_id',
        'system_name',
        'rental_cost',
        'paid_until',
    ];
}
