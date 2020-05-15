<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EveRegion extends Model
{
    //Table Name
    protected $table = 'eve_regions';

    //Timestamps
    public $timestamps = false;

    /**
     * Variables which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'region_id',
        'region_name',
    ];
}
