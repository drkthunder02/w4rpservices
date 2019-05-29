<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobProcessAssets extends Model
{
    //no table name is needed

    //Timestamps
    public $timestamps = false;

    protected $fillable = [
        'charId',
        'corpId',
        'page',
        'esi',
    ];
}
