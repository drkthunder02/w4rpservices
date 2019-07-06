<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobProcessContracts extends Model
{
    //No table name is needed

    //Timestamps
    public $timestamps = false;

    protected $fillable = [
        'charId',
        'corpId',
        'page',
    ];
}
