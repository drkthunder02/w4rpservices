<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use Commands\Library\CommandHelper;

use App\Models\ScheduledTask\ScheduleJob;

class DumpFleets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:dumpfleets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to remove all current fleets from the database';

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
        $task = new CommandHelper('DumpFleets');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Dump all fleets from the table to start a new day
        DB::table('Fleets')->delete();

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
