<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobProcessStructure extends Model
{
    //Table Name - Not Needed for a Job
    //public $table = null;

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'charId',
        'corpId',
        'page',
        'esi',
    ];
}
