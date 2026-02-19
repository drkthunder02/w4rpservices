<?php

namespace App\Console\Commands\Data;

// Internal Library
use App\Models\Structure\Asset;
// Models
use App\Models\Structure\Service;
use App\Models\Structure\Structure;
use Illuminate\Console\Command;

class EmptyJumpBridges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:EmptyJumpBridges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the jump bridge fuel related tables.';

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
        Structure::truncate();
        Service::truncate();
        Asset::truncate();
    }
}
