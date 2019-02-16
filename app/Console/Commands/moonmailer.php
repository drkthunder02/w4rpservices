<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Commands\Library\CommandHelper;
use App\Library\Moons\MoonMailer;
use App\Library\Lookups\LookupHelper;
use DB;
use Carbon\Carbon;

use App\Models\Moon\Moon;
use App\Models\Moon\MoonRent;

class MoonMailerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:MoonMailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mail out the moon rental bills automatically';

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
        //Create the new command helper container
        $task = new CommandHelper('MoonMailer');
        //Add the entry into the jobs table saying the job has started
        $task->SetStartStatus();

        

        

        //Send a mail to the contact listing the moons, and how much is owed

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
