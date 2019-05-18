<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobSendEveMail extends Model
{
    //Timestamps
    public $timestamps = true;

    protected $fillable = [
        'sender',
        'recipient',
        'recipient_type',
        'subject',
        'body',
        'created_at',
        'updated_at',
    ];
}
