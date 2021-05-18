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
use Seat\Eseye\Containers\EsiResponse;

class SendEveMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retries
     * With new rate limiting, we need a retry basis versus timeout basis
     * @var int
     */
    public $retries = 1;

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
        $this->onQueue('mail');

        //Set the middleware for the job
        $this->middleware = $this->middleware();

        //Private variables
        $this->body = $body;
        $this->recipient = $recipient;
        $this->recipient_type = $recipient_type;
        $this->subject = $subject;
        $this->sender = $sender;
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
        //Declare some variables
        $esiHelper = new Esi;
        $errorCode = null;

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

        
        $esi->setBody([
            'approved_cost' => 10000,
            'body' => $this->body,
            'recipients' => [[
                'recipient_id' => $this->recipient,
                'recipient_type' => $this->recipient_type,
            ]],
            'subject' => $this->subject,
        ])->invoke('post', '/characters/{character_id}/mail/', [
            'character_id'=> $this->sender,
        ]);
    }

    /**
     * Middleware to only allow 4 jobs to be run per minute
     * After a failed job, the job is released back into the queue for at least 1 minute x the number of times attempted
     * 
     */
    public function middleware() {
        
        //Allow 4 jobs per minute, and implement a rate limited backoff on failed jobs
        $rateLimitedMiddleware = (new RateLimited())
            ->enabled()
            ->key('psemj')
            ->connectionName('default')
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
        if(!exception instanceof RequestFailedException) {
            //If not a failure due to ESI, then log it.  Otherwise,
            //deduce why the exception occurred.
            Log::critical($exception);
        }

        if ((is_object($exception->getEsiResponse()) && (stristr($exception->getEsiResponse()->error, 'Too many errors') || stristr($exception->getEsiResponse()->error, 'This software has exceeded the error limit for ESI'))) || 
           (is_string($exception->getEsiResponse()) && (stristr($exception->getEsiResponse(), 'Too many errors') || stristr($exception->getEsiResponse(), 'This software has exceeded the error limit for ESI')))) {
            
            //We have hit the error rate limiter, wait 120 seconds before releasing the job back into the queue.
            Log::info('SendEveMail has hit the error rate limiter.  Releasing the job back into the wild in 2 minutes.');
            $this->release(120);
        }  else {
            $errorCode = $exception->getEsiResponse()->getErrorCode();

            switch($errorCode) {
                case 400:  //Bad Request
                    Log::critical("Bad request has occurred in SendEveMail.  Job has been discarded");
                    break;
                case 401:  //Unauthorized Request
                    Log::critical("Unauthorized request has occurred in SendEveMail at " . Carbon::now()->toDateTimeString() . ".\r\nCancelling the job.");
                    break;
                case 403:  //Forbidden
                    Log::critical("SendEveMail has incurred a forbidden error.  Cancelling the job.");
                    break;
                case 420:  //Error Limited
                    Log::warning("Error rate limit occurred in SendEveMail.  Restarting job in 120 seconds.");
                    $this->release(120);
                    break;
                case 500:  //Internal Server Error
                    Log::critical("Internal Server Error for ESI in SendEveMail.  Attempting a restart in 120 seconds.");
                    $this->release(120);
                    break;
                case 503:  //Service Unavailable
                    Log::critical("Service Unavailabe for ESI in SendEveMail.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 504:  //Gateway Timeout
                    Log::critical("Gateway timeout in SendEveMail.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 520:  //Internal Error -- Mostly comes when rate limited hit
                    Log::warning("Rate limit hit for SendEveMail.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 201:   //Good response code
                    $this->SaveSentRecord($this->sender, $this->subject, $this->body, $this->recipient, $this->recipient_type);
                    $this->delete();
                    break;
                //If no code is given, then log and break out of switch.
                default:
                    Log::warning("No response code received from esi call in SendEveMail.\r\n");
                    $this->delete();
                    break;
            }
        }
    }

    public function tags() {
        return ['ProcessEveMails'];
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
