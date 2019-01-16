<?php

namespace App\Models\Corporation;

use Illuminate\Database\Eloquent\Model;

class CorpTaxRatio extends Model
{
    /**
     * Table Name
     */
    protected $table = 'corp_tax_ratios';

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
        'corporation_id',
        'corporation_name',
        'structure_type',
        'ratio',
    ];
}
