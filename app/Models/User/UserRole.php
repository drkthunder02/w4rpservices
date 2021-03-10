<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    /**
     * Database Table
     */
    protected $table = 'user_roles';

    //Primary Key
    public $primaryKey = 'id';

    /**
     * Attributes which are mass assignable
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
