<?php

namespace App\Console\Commands\Assets;

use Illuminate\Console\Command;
use DB;
use Log;

//Job
use App\Jobs\Commands\Structures\ProcessAssetsJob;

//Library
use App\Library\Esi\Esi;
use Commands\Library\CommandHelper;
use App\Library\Assets\AssetHelper;
use Seat\Eseye\Exceptions\RequestFailedException;

class GetAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:GetAssets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets all of the assets of the holding corporation.';

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
        $task = new CommandHelper('GetAssets');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        ProcessAssetsJob::dispatch($charId, $corpId)->onQueue('assets');

        $task->SetStopStatus();
    }
}
