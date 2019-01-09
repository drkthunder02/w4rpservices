<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'users_role';

    protected $fillable = [
        'character_id',
        'role',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
