<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SrpPayout extends Model
{
    //Table Name
    protected $table = 'srp_payouts';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;
}
