<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

use App\Models\Mail\EveMail as EveMailModel;

class SendEveMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Class Variable for eve mail
     */
    protected $eveMail;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 120;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

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

        //Retrieve the token for main character to send mails from
        $token = EsiToken::where(['character_id'=> 93738489])->get();

        //Create the ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        //Setup the Eseye class
        $esi = new Eseye($authentication);

        //Attemp to send the mail
        try {
            $esi->setBody([
                'approved_cost' => 0,
                'body' => $mail->body,
                'recipients' => [[
                    'recipient_id' => (int)$mail->recipient,
                    'recipient_type' => $mail->recipient_type,
                ]],
                'subject' => $mail->subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id'=> 93738489,
            ]);
        } catch(RequestFailedException $e) {
            //
        }

        $mail->delete();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed($exception)
    {
        // Send user notification of failure, etc...
        dd($exception);
    }
}
