<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobProcessWalletJournal extends Model
{
    //Table Name
    //public $table = 'job_process_wallet_journal';

    //Timestamps
    public $timestamps = true;

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