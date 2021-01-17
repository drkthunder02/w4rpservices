<?php

namespace App\Console\Commands\RentalMoons;

//Internal Library
use Illuminate\Console\Command;

//Job
use App\Jobs\Commands\RentalMoons\UpdateMoonRentalPrice;

class AllianceRentalMoonUpdatePricingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:UpdateRentalPrice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update alliance rental moon prices.';

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
        UpdateMoonRentalPrice::dispatch();
    }
}
