<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

use App\Library\Esi;
use App\Library\Mail;
use App\Models\ScheduleJob;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class sendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:sendmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to a character';

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
     * Gather the taxes needed and add them together.
     * Send a mail to the character owning the ESI scope with the taxes
     * owed to the holding corp
     *
     * @return mixed
     */
    public function handle()
    {
        //Add an entry into the jobs table
        $job = new ScheduleJob;
        $time = Carbon::now();
        $job->job_name = 'SendMail';
        $job->job_state = 'Starting';
        $job->system_time = $time;
        $job->save();

        //Put our task in this section

        //If the job is finished we need to mark it in the table
        DB::table('schedule_jobs')->where('system_time', $time)->update([
            'job_state' => 'Finished',
        ]);
    }
}
