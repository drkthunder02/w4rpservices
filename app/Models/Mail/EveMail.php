<?php

namespace App\Models\Mail;

use Illuminate\Database\Eloquent\Model;

class EveMail extends Model
{
    //Table Name
    protected $table = 'eve_mails';

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
