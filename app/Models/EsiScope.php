<?php

namespace App\Models;

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

    /**
     *  The attributes that are mass assignable
     * 
     *  @var array
     */
    protected $fillable = [
        'character_id',
        'scope',
    ];
}
