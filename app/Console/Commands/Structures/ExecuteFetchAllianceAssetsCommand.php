<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Commands\Structure\FetchAllianceAssets as FAA;

class ExecuteFetchAllianceAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structure:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute fetch alliance command.';

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
     * @return int
     */
    public function handle()
    {
        FAA::dispatch()->onQueue('default');

        return 0;
    }
}
