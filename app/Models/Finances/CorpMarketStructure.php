<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorpMarketStructure extends Model
{
    //Table Name
    public $table = 'corp_market_structures';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'corporation_id',
        'tax',
        'ratio',
    ];
}
