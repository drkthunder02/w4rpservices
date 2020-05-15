<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetMarketDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:MarketData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the market data for regions';

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
