<?php

namespace App\Jobs\Commands\Structures;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Jobs
use App\Jobs\Commands\FetchAllianceStructures;

//Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;

class PurgeAllianceStructures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Structure::truncate();
        Service::truncate();

        FetchAllianceStructures::dispatch()->onQueue('structures');
    }
}
