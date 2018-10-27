<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrePrice extends Model
{
    // Table Name
    protected $table = 'OrePrices';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = 'false';
}
