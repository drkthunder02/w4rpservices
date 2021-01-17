<?php

namespace App\Console\Commands\Structures;

use Illuminate\Console\Command;

//Job
use App\Jobs\Commands\Structures\ProcessStructureJob;

class GetStructuresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:GetStructures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the list of structures ';

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
        //Get the esi config
        $config = config('esi');

        //Declare some variables
        $charId = $config['primary'];
        $corpId = 98287666;

        //Dispatch the job to be done when the application has time
        ProcessStructureJob::dispatch($charId, $corpId);
    }
}
