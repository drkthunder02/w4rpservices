<?php

namespace App\Models\SRP;

use Illuminate\Database\Eloquent\Model;

class SrpShipType extends Model
{
    //Table Name
    protected $table = 'srp_ship_types';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;
}
