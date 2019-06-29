<?php

namespace App\Models\SRP;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    //Table Name
    protected $table = 'srp_ships';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    //Fillable Items
    protected $fillable = [
        'ship_type',
        'character_id',
        'zkillboard',
        'notes',
    ];
}
