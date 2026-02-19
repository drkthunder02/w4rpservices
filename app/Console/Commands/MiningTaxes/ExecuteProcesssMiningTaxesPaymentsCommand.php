<?php

namespace App\Console\Commands\MiningTaxes;

use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPayments as PMTP;
use Illuminate\Console\Command;

class ExecuteProcesssMiningTaxesPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Mining Taxes payments from the console.';

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
        PMTP::dispatch();

        return 0;
    }
}
