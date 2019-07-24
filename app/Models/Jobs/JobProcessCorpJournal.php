<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobProcessCorpJournal extends Model
{
    //No table name is needed

    //Timestamps
    public $timestamps = false;

    protected $fillable = [
        'division',
        'charId',
        'corpId',
        'page',
    ];
}
