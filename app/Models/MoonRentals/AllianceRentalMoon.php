<?php

namespace App\Models\MoonRentals;

use Illuminate\Database\Eloquent\Model;

class AllianceRentalMoon extends Model
{
    //Table Name
    protected $table = 'alliance_rental_moons';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    /**
     * Items which are mass assignable by the model
     * 
     * @var array
     */
    protected $fillable = [
        'region',
        'system',
        'planet',
        'moon',
        'structure_id',
        'structure_name',
        'first_ore',
        'first_quantity',
        'second_ore',
        'second_quantity',
        'third_ore',
        'third_quantity',
        'fourth_ore',
        'fourth_quantity',
        'moon_worth',
        'alliance_rental_price',
        'out_of_alliance_rental_price',
        'rental_type',
        'rental_until',
        'rental_contact_id',
        'rental_contact_type',
        'paid',
        'paid_until',
        'alliance_use_until',
    ];


}
