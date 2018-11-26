<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

use App\Library\MoonCalc;

class UpdateMoonPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:updatemoonprice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update moon pricing on a scheduled basis';

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
        //Set the time the job has started
        $job = new ScheduleJob;
        $time = Carbon::now();
        $job->job_name = 'CorpJournal';
        $job->job_state = 'Starting';
        $job->system_time = $time;
        $job->save();

        $moonCalc = new MoonCalc();
        $moonCalc->FetchNewPrices();

        //Set the state of the job as finished
        DB::table('schedule_jobs')->where('system_time', $time)->update([
            'job_state' => 'Finished',
        ]);
    }
}
