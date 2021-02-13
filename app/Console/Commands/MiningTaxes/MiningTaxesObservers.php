<?php

namespace App\Console\Commands\MiningTaxes;

use Illuminate\Console\Command;

class MiningTaxesObservers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get mining tax observers.';

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
