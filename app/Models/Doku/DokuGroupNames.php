<?php

namespace App\Models\Doku;

use Illuminate\Database\Eloquent\Model;

class DokuGroupNames extends Model
{
    // Table Name
    protected $table = 'wiki_groupnames';

    public $timestamps = false;

    protected $fillable = [
        'gname',
        'description',
    ];
}
