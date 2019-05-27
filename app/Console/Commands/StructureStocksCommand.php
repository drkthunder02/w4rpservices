<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Commands\Library\CommandHelper;

use App\Models\ScheduledTask\ScheduleJob;

class StructureStocksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:StationStocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the alliance stocks which are for sale from esi.';

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
        $task = new CommandHelper('StationStocks');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();


        //Mark the job as finished
        $task->SetStopStatus();
    }
}
