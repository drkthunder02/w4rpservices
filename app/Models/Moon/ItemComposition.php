<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class ItemComposition extends Model
{
    // Table Name
    protected $table = 'ItemComposition';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;
}
