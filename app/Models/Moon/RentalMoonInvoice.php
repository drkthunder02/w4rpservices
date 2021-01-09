<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class RentalMoonInvoice extends Model
{
    //Table Name
    protected $table = 'alliance_moon_rental_invoices';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
        'invoice_id',
        'rental_moons',
        'invoice_amount',
        'due_date',
        'paid',
    ];
}
