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
     * With new rate limiting, we need a retry basis versus timeout basis
     * @var int
     */
    public $retries = 5;

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
        $response = null;

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
        } catch(RequestFailedException $e) {
            Log::warning($e);
            $this->release(20);
        }

        if($response == null) {
            $this->release(30);
        }

        //Get the error code and take the appropriate action
        $errorCode = $response->getErrorCode();

        switch($errorCode) {
            case 400:  //Bad Request
                $this->release(15);
                break;
            case 401:  //Unauthorized Request
                $this->release(15);
                break;
            case 403:  //Forbidden
                $this->release(15);
                break;
            case 420:  //Error Limited
                Log::warning("Error rate limit occurred in ProcessSendEveMailJob.  Restarting job in 120 seconds.");
                $this->release(120);
                break;
            case 500:  //Internal Server Error
                Log::critical("Internal Server Error for ESI in ProcessSendEveMailJob");
                return 0;
                break;
            case 503:  //Service Unavailable
                Log::critical("Service Unavailabe for ESI in ProcessSendEveMailJob");
                $this->release(15);
                break;
            case 504:  //Gateway Timeout
                Log::critical("Gateway timeout in ProcessSendEveMailJob");
                $this->release(15);
                break;
            case 520:  //Internal Error -- Mostly comes when rate limited hit
                $this->release(15);
                break;
            default:   //If not an error, then just break out of the switch statement
                break;
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
            ->enabled()
            ->key('psemj')
            ->connectionName('redis')
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
