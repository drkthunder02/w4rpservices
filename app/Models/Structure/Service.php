<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //Table Name
    public $table = 'structure_services';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'structure_id',
        'name',
        'state',
    ];
}
