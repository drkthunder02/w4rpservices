<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Commands\MiningTaxes\SendMiningTaxesInvoices;

class ExecuteSendMiningTaxesInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'miningtax:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute send mining tax invoices.';

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
        SendMiningTaxesInvoices::dispatch();

        return 0;
    }
}
