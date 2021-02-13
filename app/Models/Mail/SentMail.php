<?php

namespace App\Models\Mail;

use Illuminate\Database\Eloquent\Model;

class SentMail extends Model
{
    //Table Name
    protected $table = 'sent_mails';

    //Timestamps
    public $timestamps = false;

    protected $fillable = [
        'sender',
        'recipient',
        'reicpient_type',
        'subject',
        'body',
    ];
}
