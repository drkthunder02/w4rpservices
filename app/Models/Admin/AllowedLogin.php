<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AllowedLogin extends Model
{
    // Table Name
    public $table = 'allowed_login';

    // Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
    ];
}
