<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    // Table Name
    protected $table = 'Prices';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;
}
