<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $fillable = [
        'character_id',
        'role',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
