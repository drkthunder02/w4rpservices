<?php

namespace App\Models\SRP;

use Illuminate\Database\Eloquent\Model;

class SrpPayout extends Model
{
    //Table Name
    protected $table = 'srp_payouts';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    //Fillable
    protected $fillable = [
        'code',
        'payout',
    ];

    public function shipType() {
        return $this->belongsTo(SrpShipType::class, 'code', 'code');
    }
}
