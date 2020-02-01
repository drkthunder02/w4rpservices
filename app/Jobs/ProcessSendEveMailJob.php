<?php

namespace App\Jobs;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;

//Models
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Mail\EveMail;
use App\Models\Jobs\JobStatus;
use App\Models\Mail\SentMail;
use App\Models\Jobs\JobSendEveMail;

class ProcessSendEveMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

    private $sender;
    private $body;
    private $recipient;
    private $recipient_type;
    private $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobSendEveMail $mail) {
        $this->body = $mail->body;
        $this->recipient = $mail->recipient;
        $this->recipient_type = $mail->recipient_type;
        $this->subject = $mail->subject;
        $this->sender = $mail->sender;

        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * Utilized by using ProcessSendEveMailJob::dispatch($mail);
     * The model is passed into the dispatch function, then added to the queue
     * for processing.
     *
     * @return void
     */
    public function handle()
    {
        //Declare some variables
        $esiHelper = new Esi;

        //Get the esi configuration
        $config = config('esi');

        //Retrieve the token for main character to send mails from
        $token = EsiToken::where(['character_id'=> $this->sender])->first();

        //Create the ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Attemp to send the mail
        try {
            $esi->setBody([
                'approved_cost' => 100,
                'body' => $this->body,
                'recipients' => [[
                    'recipient_id' => $this->recipient,
                    'recipient_type' => $this->recipient_type,
                ]],
                'subject' => $this->subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id'=> $this->sender,
            ]);
        } catch(RequestFailedException $e) {
            Log::warning($e);
            return null;
        }

        $this->SaveSentRecord($this->sender, $this->subject, $this->body, $this->recipient, $this->recipient_type);
        
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
        Log::critical($exception);
    }

    private function SaveSentRecord($sender, $subject, $body, $recipient, $recipientType) {
        $sentmail = new SentMail;
        $sentmail->sender = $sender;
        $sentmail->subject = $subject;
        $sentmail->body = $body;
        $sentmail->recipient = $recipient;
        $sentmail->recipient_type = $recipientType;
        $sentmail->save();
    }
}
