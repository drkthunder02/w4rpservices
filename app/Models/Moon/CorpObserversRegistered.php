<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class CorpObserversRegistered extends Model
{
    //Table Name
    protected $table = 'corp_mining_observers_registered';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * Attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
    ];
}
