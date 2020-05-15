<?php

namespace App\Console\Commands;

//Internal Library
use Illuminate\Console\Command;
use Log;

//Library
use Commands\Library\CommandHelper;

//Jobs
use App\Jobs\Commands\Market\PurgeMarketRegionOrderJob;

class PurgeMarketDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:PurgeMarketData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges old market data from the database';

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
        PurgeMarketDataJob::dispatch();
    }
}
