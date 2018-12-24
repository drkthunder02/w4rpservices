<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpDeskTicket extends Model
{
    //Table Name
    protected $table = 'help_desk_tickets';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'assigned_id',
        'department',
        'subject',
        'body',
    ];
}
