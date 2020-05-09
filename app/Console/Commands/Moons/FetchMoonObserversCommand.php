<?php

namespace App\Console\Commands\Moons;

use Illuminate\Console\Command;

class FetchMoonObserversCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:FetchMoonObservers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Moon Observers';

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
        //Create the command helper container
        $task = new CommandHelper('FetchMoonObservers');
        //Add the entry into the jobs table saying the job has started
        $task->SetStartStatus();

        //Declare some variables
        $lookup  = new LookupHelper;
        $esi = new Esi;

        //Get the configuration from the main site
        $config = config('esi');

        
    }
}
