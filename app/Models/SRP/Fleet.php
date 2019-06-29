<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    //Table Name
    protected $table = 'srp_fleet';

    //Primary Key
    public $primaryKey = 'fleet_id';

    //Timestamps
    public $timestamps = true;

    //Fillable Items
    protected $fillable = [
        'fleet_id',
        'fleet_name',
        'fleet_commander',
        'fleet_commander_id',
        'fleet_type',
        'fleet_description',
    ];
}
