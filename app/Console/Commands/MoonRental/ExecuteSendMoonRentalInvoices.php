<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecuteSendMoonRentalInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mr:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute command to send moon rental invoices job';

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
