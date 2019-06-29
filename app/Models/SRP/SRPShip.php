<?php

namespace App\Models\SRP;

use Illuminate\Database\Eloquent\Model;

class SRPShip extends Model
{
    //Table Name
    protected $table = 'srp_ships';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    //Fillable Items
    protected $fillable = [
        'character_id',
        'character_name',
        'fleet_commander_name',
        'fleet_commander_id',
        'zkillboard',
        'ship_type',
        'loss_value',
        'notes',
        'approved',
        'paid_value',
        'paid_by_id',
        'paid_by_name',
    ];
}
