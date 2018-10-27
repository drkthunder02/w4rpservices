<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokuUser extends Model
{
    // Table Name
    protected $table = 'wiki_user';

    // Timestamps
    public $timestamps = 'false';
}
