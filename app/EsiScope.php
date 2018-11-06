<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EsiScope extends Model
{
    // Table Name
    protected $table = 'UserEsiScopes';

    // Timestamps
    public $timestamps = true;

    public function user() {
        return $this->belongsTo('App\User', 'character_id', 'character_id');
    }
}
