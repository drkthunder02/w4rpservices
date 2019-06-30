<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SrpFleetType extends Model
{
    //Table Name
    protected $table = 'srp_fleet_types';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;
}
