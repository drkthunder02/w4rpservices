<?php

namespace App\Jobs\Commands\Eve;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\RateLimitedMiddleware\RateLimited;
use Log;
use Carbon\Carbon;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;

//Models
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;
use App\Models\Jobs\JobStatus;
use App\Models\Mail\SentMail;

class ProcessSendEveMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * With new rate limiting, we shouldn't use this timeout
     * @var int
     */
    //public $timeout = 3600;

    /**
     * Retries
     * With new rate limiting, we shouldn't use this timeout
     * @var int
     */
    //public $retries = 3;

    /**
     * Middleware for the job
     * 
     * @var \Spatie\RateLimitedMiddleware\RateLimited
     */
    private $middleware;

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
    public function __construct($body, $recipient, $recipient_type, $subject, $sender) {
        //Set the connection
        $this->connection = 'redis';

        //Private variables
        $this->body = $body;
        $this->recipient = $recipient;
        $this->recipient_type = $recipient_type;
        $this->subject = $subject;
        $this->sender = $sender;
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
        $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
        //Create the ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

        //Check to see if the token is valid or not
        if($esiHelper->TokenExpired($refreshToken)) {
            $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
            $esi = $esiHelper->SetupEsiAuthentication($refreshToken);
        }

        //Attemp to send the mail
        try {
            $reponse = $esi->setBody([
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
     * Middleware to only allow 4 jobs to be run per minute
     * After a failed job, the job is released back into the queue for at least 1 minute x the number of times attempted
     * 
     */
    public function middleware() {
        
        //Allow 4 jobs per minute, and implement a rate limited backoff on failed jobs
        $rateLimitedMiddleware = (new RateLimited())
            ->allow(4)
            ->everySeconds(60)
            ->releaseAfterOneMinute()
            ->releaseAfterBackoff($this->attempts());

        return [$rateLimitedMiddleware];
    }

    /*
    * Determine the time at which the job should timeout.
    *
    */
    public function retryUntil() :  \DateTime
    {
        return Carbon::now()->addDay();
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
