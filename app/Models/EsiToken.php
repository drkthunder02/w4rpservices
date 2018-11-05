<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsiToken extends Model
{
    // Table Name
    protected $table = 'EsiTokens';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;
}
