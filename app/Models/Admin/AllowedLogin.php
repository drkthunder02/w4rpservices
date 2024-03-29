<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AllowedLogin extends Model
{
    //Table Name
    public $table = 'allowed_logins';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'login_type',
    ];
}
