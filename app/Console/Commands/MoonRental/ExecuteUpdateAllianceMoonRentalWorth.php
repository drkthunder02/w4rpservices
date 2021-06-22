<?php

namespace App\Console\Commands\MoonRental;

use Illuminate\Console\Command;

use App\Jobs\Commands\MoonRental\UpdateAllianceMoonRentalWorth;

class ExecuteUpdateAllianceMoonRentalWorth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mr:worth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update alliance moon rental worth.';

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
        UpdateAllianceMoonRentalWorth::dispatch();

        return 0;
    }
}
