<?php

namespace App\Console\Commands\Eve;

use Illuminate\Console\Command;

//Library
use App\Library\Moons\MoonCalc;

//Job
use App\Jobs\Commands\Eve\ItemPricesUpdateJob;

class ItemPricesUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:ItemPriceUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update mineral and ore prices';

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
        $moonHelper = new MoonCalc;
        $moonHelper->FetchNewPrices();

        //ItemPricesUpdateJob::dispatch()->onQueue('default');
    }
}
