<?php

namespace App\Console\Commands\MiningTaxes;

use App\Jobs\Commands\MiningTaxes\PreFetchMiningTaxesLedgers as PreFetch;
use Illuminate\Console\Command;

class ExecuteMiningTaxesLedgersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt:ledgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute mining taxes ledgers jobs.';

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
        PreFetch::dispatch();

        return 0;
    }
}
