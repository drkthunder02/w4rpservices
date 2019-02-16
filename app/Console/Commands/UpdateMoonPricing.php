<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Commands\Library\CommandHelper;

use App\Library\Moons\MoonCalc;

class UpdateMoonPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:UpdateMoonPrice';

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
        //Create the command helper container
        $task = new CommandHelper('CorpJournal');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        $moonCalc = new MoonCalc();
        $moonCalc->FetchNewPrices();

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
