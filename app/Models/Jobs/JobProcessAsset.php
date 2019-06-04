<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobProcessAsset extends Model
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
