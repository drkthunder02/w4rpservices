<?php

namespace App\Models\MoonRentals;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'next_moon_pull',
    ];

    public function getPaidStatus() {
        return $this->paid;
    }

    public function getNextMoonPull() {
        return $this->next_moon_pull;
    }

    public function getRSPM() {
        return $this->region . " - " . $this->system . " - " . $this->planet . " - " . $this->moon;
    }

    public function getOOARentalPrice() {
        return $this->out_of_alliance_rental_price;
    }

    public function getIARentalPrice() {
        return $this->alliance_rental_price;
    }

    public function getWorth() {
        return $this->moon_worth;
    }

    public function getRentalType() {
        return $this->rental_type;
    }

    public function isRented() {
        $today = Carbon::now();

        if($today->lessThan($this->rental_until)) {
            return true;
        } else {
            return false;
        }
    }

    public function isPaid() {
        $today = Carbon::now();

        if($today->lessThan($this->paid_until)) {
            return true;
        } else {
            return false;
        }
    }
}
