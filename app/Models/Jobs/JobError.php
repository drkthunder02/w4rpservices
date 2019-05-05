<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobError extends Model
{
    //Table Name
    public $table = 'job_statuses';

    //Timestamps
    public $timestaps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'job_id',
        'job_name',
        'error',
    ];

    public function JobStatus() {
        $this->belongsTo('\App\Models\Jobs\JobStatus');
    }
}
