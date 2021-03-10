<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    /**
     * Database Table
     */
    protected $table = 'user_permissions';

    //Primary Key
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'permission',
    ];

    

    public function user() {
        return $this->belongsTo(User::class);
    }
}
