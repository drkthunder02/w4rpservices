<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsiScope extends Model
{
    // Table Name
    protected $table = 'EsiScopes';

    // Timestamps
    public $timestamps = true;

    /**
     *  The attributes that are mass assignable
     * 
     *  @var array
     */
    protected $fillable = [
        'character_id',
        'scope',
    ];

    public function user() {
        return $this->belongsTo('App\User', 'character_id', 'character_id');
    }
}
