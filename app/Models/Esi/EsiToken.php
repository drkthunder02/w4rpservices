<?php

namespace App\Models\Esi;

use Illuminate\Database\Eloquent\Model;

class EsiToken extends Model
{
    // Table Name
    protected $table = 'EsiTokens';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'access_token',
        'refresh_token',
        'inserted_at',
        'expires_in',
    ];

    public function user() {
        return $this->belongsTo(App\Models\User\User::class, 'character_id', 'character_id');
    }

    public function esiscopes() {
        return $this->hasMany(App\Models\EsiScope::class, 'character_id', 'character_id');
    }
}
