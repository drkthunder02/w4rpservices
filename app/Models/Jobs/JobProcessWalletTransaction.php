<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobProcessWalletTransaction extends Model
{
    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'charId',
        'division',
        'page',
    ];
}

?>
