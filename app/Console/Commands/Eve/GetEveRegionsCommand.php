<?php

namespace App\Console\Commands\Eve;

//Internal Library
use Illuminate\Console\Command;

//Library
use Commands\Library\CommandHelper;

//Job
use App\Jobs\Commands\Eve\GetEveRegionsJob;

class GetEveRegionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eve:getRegions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets all of the information for regions from the eve esi';

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
        GetEveRegionsJob::dispatch();
    }
}
