<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Jobs\SendEveMail;
use Commands\Library\CommandHelper;
use App\Library\Esi\Esi;
use App\Library\Esi\Mail;
use App\Library\Clones\CloneSaver;

use App\Models\Character\CharacterClone;

class RunCloneSaver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:clonesaver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs the functionality for clone saver';

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
        $task = new CommandHelper('CloneSaver');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();



        

        //Mark the job as finished
        $task->SetStopStatus();
    }

    private function CloneSaverMail() {
        //Setup time frame job has been sent so we don't send too many mails

        
        //Store a new eve mail model for the job to dispatch
        $mail = new EveMail;
        $mail->sender = $self;
        $mail->subject = 'Clone Saver Alert';
        $mail->body = 'You have failed to change clones before undocking.<br>Please be advised we believe you should change your clones due to your expensive implants.<br>Sincerely,<br>The Clone Saver Team';
        $mail->recipient = (int)$self;
        $mail->recipient_type = 'character';

        //Dispatch the job
        SendEveMail::dispatch($mail)->delay(Carbon::now()->addSeconds(1));
    }
}
