<?php

namespace App\Jobs\Commands\MoonRental\Invoices;

// Application Library
use App\Library\Helpers\LookupHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
// Internal Library
use Illuminate\Queue\SerializesModels;

// Models

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
        // Declare variables
        $lookup = new LookupHelper;
        $months = 3;
        $today = Carbon::now();
        $future = Carbon::now()->addMonths(3);
    }
}
