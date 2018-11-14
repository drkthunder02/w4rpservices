<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    // Table Name
    protected $table = 'Fleets';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'fleet',
        'description',
        'creation_time',
        'time_left',
    ];
}
