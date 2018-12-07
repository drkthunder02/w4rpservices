<?php

namespace Commands\Library;

use DB;
use Carbon\Carbon;

use App\Models\ScheduleTask\ScheduleJob;

class CommandHelper {

    private $job_name;
    private $job_state;
    private $system_time;

    public function __construct($name) {
        $this->job_name = $name;
        $this->job_status = 'Starting';
        $this->system_time = Carbon::now();
    }

    public function SetStartStatus() {
        //Add an entry into the jobs table
        $job = new ScheduleJob;
        $job->job_name = $this->job_name;
        $job->state = $this->job_state;
        $job->system_time = $this->system_time;
        $job->save();
    }

    public function SetStopStatus() {
        //Mark the job as finished
        DB::table('schedule_jobs')->where([
            'system_time' => $this->system_time,
            'job_name' => $this->job_name,
        ])->update([
            'job_state' => 'Finished',
        ]);
    }

}

?>