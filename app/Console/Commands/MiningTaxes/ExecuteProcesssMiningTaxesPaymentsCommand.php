<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPayemnts as PMTP;

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
    protected $description = 'Command description';

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
