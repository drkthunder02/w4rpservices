<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthAccount extends Model
{
    protected $fillable = [
        'name', 'email', 'avatar', 'owner_hash', 'id', 'expiresIn', 'token', 'refreshToken',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
