<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moon extends Model
{
    // Table Name
    protected $table = 'Moons';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = 'false';
}
