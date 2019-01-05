<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;

class StructureIndustryTaxJournal extends Model
{
    /**
     * Table Name
     */
    protected $table = 'structure_industry_tax_journal';

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
        'id',
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
