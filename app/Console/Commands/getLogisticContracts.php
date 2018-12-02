<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

use App\Models\Logistics\Contract;
use App\Models\ScheduleJob;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class getLogisticContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:logistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the logistics jobs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $job = new ScheduleJob;
        $time = Carbon::now();
        $job->job_name = "GetLogisticsContracts";
        $job->job_state = 'Starting';
        $job->system_time = $time;
        $job-save();

        //Create functionality to record contracts for logistical services

        //If the job is finished we need to mark it in the table
        DB::table('schedule_jobs')->where('system_time', $time)->update([
            'job_state' => 'Finished',
        ]);
    }
}
