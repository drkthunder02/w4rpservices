<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobSendEveMail extends Model
{
    //Timestamps
    public $timestamps = false;

    protected $fillable = [
        'sender',
        'recipient',
        'recipient_type',
        'subject',
        'body',
    ];
}
