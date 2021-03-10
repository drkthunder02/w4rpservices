<?php

namespace App\Models\ScheduledTask;

use Illuminate\Database\Eloquent\Model;

class ScheduleJob extends Model
{
    //Table Name
    protected $table = 'schedule_jobs';

    //Timestamps
    public $timestamps = true;

    //Primary Key
    public $primaryKey = 'id';

    protected $fillable = [
        'job_name',
        'job_state',
    ];
}
