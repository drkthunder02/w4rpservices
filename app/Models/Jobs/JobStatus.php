<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
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
        'id',
        'job_name',
        'complete',
    ];

    public function Error() {
        $this->hasOne('\App\Models\Jobs\JobError', 'job_id', 'id');
    }
}
