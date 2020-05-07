<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class CorpMoonObserver extends Model
{
    //Table Name
    protected $table = 'corp_mining_observers';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    protected $fillable = [
        'corporation_id',
        'corporation_name',
        'observer_id',
        'observer_name',
        'observer_type',
        'last_updated',
    ];  
}
