<?php

namespace App\Jobs;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

//Seat stuff
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Mail\EveMail;
use App\Models\Jobs\JobError;
use App\Models\Jobs\JobStatus;

class SendEveMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    private $body;
    private $recipient;
    private $recipient_type;
    private $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EveMail $mail) {
        $this->body = $mail->body;
        $this->recipient = $mail->recipient;
        $this->recipient_type = $mail->recipient_type;
        $this->subject = $mail->subject;

        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * Utilized by using SendEveMailJob::dispatch($mail);
     * The model is passed into the dispatch function, then added to the queue
     * for processing.
     *
     * @return void
     */
    public function handle()
    {
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
                'body' => $this->body,
                'recipients' => [[
                    'recipient_id' => (int)$this->recipient,
                    'recipient_type' => $this->recipient_type,
                ]],
                'subject' => $this->subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id'=> 93738489,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }
        
        $this->delete();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed($exception)
    {
        dd($exception);
    }
}
