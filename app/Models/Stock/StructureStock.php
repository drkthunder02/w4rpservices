<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class StructureStock extends Model
{
    //Table Name
    public $table = '';

    //Timestamps
    public $tiemestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'structure_name',
        'structure_id',
        'location_flag',
        'location_id',
        'location_type',
        'item_id',
        'type_id',
        'quantity',
    ];
}
