<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Commands\Library\CommandHelper;

use App\Models\Logistics\Contract;
use App\Models\ScheduledTask\ScheduleJob;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class GetLogisticsContracts extends Command
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
        //Create the command helper container
        $task = new CommandHelper('CorpJournal');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Create functionality to record contracts for logistical services

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
