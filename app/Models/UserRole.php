<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    // Table Name
    protected $table = 'user_roles';

    // Timestamps 
    public $timestamps = true;

    public function user() {
        return $this->belongsTo('App\User', 'character_id', 'user_id');
    }
}
