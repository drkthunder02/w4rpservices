<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moon extends Model
{
    // Table Name
    protected $table = 'moons';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = 'true';
}
