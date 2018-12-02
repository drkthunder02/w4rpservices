<?php

namespace App\Models\Corporation;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    // Table Name
    protected $table = 'Structures';

    // Timestamps
    public $timestamps = true;

    //Primary Key
    public $primaryKey = 'structure_id';
}
