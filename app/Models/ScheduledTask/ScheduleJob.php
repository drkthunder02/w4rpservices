<?php

namespace App\Models\ScheduledTask;

use Illuminate\Database\Eloquent\Model;

class ScheduleJob extends Model
{
    protected $table = 'schedule_jobs';

    public $timestamps = true;

    public $primaryKey = 'id';

    protected $fillable = [
        'job_name',
        'job_state',
    ];
}
