<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    // Table Name
    protected $table = 'logistics_contracts';

    // Timestamps
    public $timestamps = true;

    // Primary Key
    public $primaryKey = 'id';
}
