<?php

namespace App\Jobs\Commands\Eve;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//Library
use App\Library\Moons\MoonCalc;

class ItemPricesUpdateJob implements ShouldQueue
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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $moonHelper = new MoonCalc;

        $moonHelper->FetchNewPrices();
    }
}
