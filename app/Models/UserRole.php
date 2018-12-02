<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    // Table Name
    protected $table = 'user_roles';

    // Timestamps 
    public $timestamps = true;

    /**
     *  The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'role',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
