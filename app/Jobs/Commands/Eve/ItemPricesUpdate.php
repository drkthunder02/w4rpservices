<?php

namespace App\Jobs\Commands\Eve;

use App\Library\Moons\MoonCalc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
// Library
use Illuminate\Queue\SerializesModels;

class ItemPricesUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Number of job retries
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = 'redis';
        $this->onQueue('default');
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

    /**
     * Set the tags for Horzion
     *
     * @var array
     */
    public function tags()
    {
        return ['Eve', 'ItemPricesUpdate'];
    }
}
