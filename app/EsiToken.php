<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsiToken extends Model
{
    // Table Name
    protected $table = 'EsiTokens';

    //Primary Key
    public $primaryKey = 'CharacterId';
}

