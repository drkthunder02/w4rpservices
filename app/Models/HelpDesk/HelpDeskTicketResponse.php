<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpDeskTicketResponse extends Model
{
    //Table Name
    protected $table = 'help_desk_ticket_responses';

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
        'assigned_id',
        'body',
    ];
}
