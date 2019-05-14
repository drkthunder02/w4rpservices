<?php

namespace App\Jobs\Library;

//Inertnal Libraries
use DB;
use Carbon\Carbon;

use App\Models\Jos\JobStatus;

class JobHelper {
    private $job_name;
    private $complete;
    private $system_time;

    protected $jobStatus;

    public function __construct($name) {
        $this->job_name = $name;
        $this->complete = false;
        $this->system_time = Carbon::now();
    }

    public function SetStartStatus() {
        $this->jobStatus = new JobStatus;
        $this->jobStatus->job_name = $this->job_name;
        $this->jobStatus->complete = $this->complete;
        $this->jobStatus->system_time = $this->system_time;

        $job->save();
    }

    public function SetStopStatus() {
        $this->jobStatus::update([
            'complete' => true,
        ]);
    }

    public function CleeanJobStatusTable() {
        DB::table('job_statuses')->where('system_time', '<', Carbon::now()->subMonths(3))->delete();
    }
}