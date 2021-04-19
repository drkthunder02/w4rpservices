<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Commands\MiningTaxes\PreFetchMiningTaxesLedgers;

class ExecuteMiningTaxesLedgersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'miningtax:ledgers';

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
        PreFetchMiningTaxesLedgers::dispatch();

        return 0;
    }
}
