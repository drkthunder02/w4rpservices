<?php

namespace App\Console\Commands\MiningTaxes;

use Illuminate\Console\Command;

class MiningTaxesInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mining Taxes Invoice Command';

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
        return 0;
    }
}
