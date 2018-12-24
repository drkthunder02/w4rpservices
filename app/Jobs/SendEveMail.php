<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Library\Esi\Mail;

use App\Models\EveMail as EveMailModel;

class SendEveMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Class Variable for eve mail
     */
    protected $eveMail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EveMailModel $mail) {
        $this->eveMail = $mail;
    }

    /**
     * Execute the job.
     * Utilized by using SendEveMail::dispatch($mail);
     * The model is passed into the dispatch function, then added to the queue
     * for processing.
     *
     * @return void
     */
    public function handle()
    {
        //Access the model in the queue for processing
        $mail = $this->eveMail;

        //Create a new Mail class variable
        $esi = new Mail();

        //Process the mail from the model to send a new mail
        $esi->SendGeneralMail($mail->recepient, $mail->subject, $mail->body);

    }

    /**
     * Determine the time the job should timeout
     * 
     * @return \DateTime
     */
    public function retryUntil() {
        return now()->addSeconds(5);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
