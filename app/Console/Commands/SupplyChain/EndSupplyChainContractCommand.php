<?php

namespace App\Console\Commands\SupplyChain;

use Illuminate\Console\Command;

class EndSupplyChainContractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:supplychain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and ends any supply chain contracts needs to be closed.';

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
        //
    }
}
