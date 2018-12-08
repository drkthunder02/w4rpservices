<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use Commands\Library\CommandHelper;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Library\Mail;
use App\Models\ScheduledTask\ScheduleJob;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class sendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:sendmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to a character for taxes owed.';

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
     * Gather the taxes needed and add them together.
     * Send a mail to the character owning the ESI scope with the taxes
     * owed to the holding corp
     *
     * @return mixed
     */
    public function handle()
    {
        //Create the command helper container
        $task = new CommandHelper('CorpJournal');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();

        //Put our task in this section
        $mail = new Mail;

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
