<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpJournal extends Model
{
    /**
     * Table Name
     */
    protected $table = 'CorpJournals';

    /**
     *  Timestamps
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'corporation_id',
        'division',
        'amount',
        'balance',
        'context_id',
        'context_id_type',
        'date',
        'description',
        'first_party_id',
        'reason',
        'ref_type',
        'second_party_id',
        'tax',
        'tax_receiver_id',
    ];

}
