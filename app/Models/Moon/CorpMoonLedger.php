<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class CorpMoonLedger extends Model
{
    //Table Name
    protected $table = 'corp_moon_ledgers';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    protected $fillable = [
        'corporation_id',
        'corporation_name',
        'character_id',
        'character_name',
        'observer_id',
        'observer_name',
        'type_id',
        'ore',
        'quantity',
        'recorded_corporation_id',
        'recorded_corporation_name',
        'last_updated',
    ];
}
