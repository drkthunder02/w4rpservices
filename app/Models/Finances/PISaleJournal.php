<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;

class PISaleJournal extends Model
{
    /**
     * Table Name
     */
    protected $table = 'pi_sale_journal';

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
        'division',
        'client_id',
        'date',
        'is_buy',
        'journal_ref_id',
        'location_id',
        'quantity',
        'transaction_id',
        'type_id',
        'unit_price',
    ];
}
