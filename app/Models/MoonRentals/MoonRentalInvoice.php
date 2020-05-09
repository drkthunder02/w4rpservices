<?php

namespace App\Models\MoonRent;

use Illuminate\Database\Eloquent\Model;

class MoonRentalInvoice extends Model
{
    //Table Name
    protected $table = 'alliance_moon_rental_invoices';

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
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
        'rental_moons',
        'invoice_amount',
        'due_date',
        'paid',
    ];
}
