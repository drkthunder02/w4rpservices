<?php

namespace App\Models\MoonRentals;

use Illuminate\Database\Eloquent\Model;

class MoonRentalPayment extends Model
{
    //Table Name
    protected $table = 'alliance_moon_rental_payments';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * These are the items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'payment_amount',
        'reference_id',
    ];
}
