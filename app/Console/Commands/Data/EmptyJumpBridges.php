<?php

namespace App\Console\Commands\Data;

//Internal Library
use Illuminate\Console\Command;

//Library
use Commands\Library\CommandHelper;

//Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;
use App\Models\Stock\Asset;

class EmptyJumpBridges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:EmptyJumpBridges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the jump bridge fuel related tables.';

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
        $task = new CommandHelper('EmptyJumpBridges');

        //Add entry into the table saying the job is starting
        $task->SetStartStatus();

        Structure::truncate();
        Service::truncate();
        Asset::truncate();

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
