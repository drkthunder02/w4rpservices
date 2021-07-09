<?php

namespace App\Jobs\Commands\MoonRental\Invoices;

//Application Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Carbon\Carbon;

//Internal Library
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MoonRental\AllianceMoon;
use App\Models\MoonRental\AllianceMoonOre;
use App\Models\MoonRental\AllianceMoonRental;

class SendMoonRentalInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = 'redis';
        $this->onQueue('miningtaxes');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $lookup = new LookupHelper;
        $months = 3;
        $today = Carbon::now();
        $future = Carbon::now()->addMonths(3);
    }
}
