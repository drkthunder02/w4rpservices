<?php

namespace App\Console\Commands\MiningTaxes;

use Illuminate\Console\Command;

use App\Jobs\Commands\MiningTaxes\FetchMiningTaxesObservers;

class ExecuteMinigTaxesObserversCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'miningtax:observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a mining tax observer job.';

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
        FetchMiningTaxesObservers::dispatch();

        return 0;
    }
}
