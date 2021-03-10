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

    //Fillable
    protected $fillable = [
        'code',
        'description',
    ];

    public function costCode() {
        return $this->hasOne(App\Models\SRP\SrpPayout::class, 'code', 'code');
    }

    public function fleetType() {
        return $this->belongsTo(App\Models\SRP\SrpFleetType::class, 'code', 'code');
    }
}
