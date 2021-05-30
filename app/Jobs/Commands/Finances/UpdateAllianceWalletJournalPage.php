<?php

namespace App\Jobs\Commands\Finances;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Log;


//Application Library
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use App\Library\Esi\Esi;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\Finances\AllianceWalletJournal;

class UpdateAllianceWalletJournalPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Number of job retries
     * 
     * @var int
     */
    public $tries = 3;

    private $division;
    private $charId;
    private $page;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($division, $charId, $page)
    {
        $this->connection = 'redis';
        $this->onQueue('finances');

        $this->division = $division;
        $this->charId = $charId;
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables in the handler
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Setup the esi container.
        $token = $esiHelper->GetRefreshToken($this->charId);
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Check the scope
        if(!$esiHelper->HaveEsiScope($this->charId, 'esi-wallet.read_corporation_wallets.v1')) {
            Log::critical('Scope check failed for esi-wallet.read_corporation_wallets.v1 for character id: ' . $charId);
            return null;
        }

        if($esiHelper->TokenExpired($token)) {
            $token = $esiHelper->GetRefreshToken($this->charId);
            $esi = $esiHelper->SetupEsiAuthentication($token);
        }

        //Reference the character id to the corporation id
        $char = $lookup->GetCharacterInfo($this->charId);
        $corpId = $char->corporation_id;

        /**
         * Attempt to get the data from the esi api.  If it fails, we skip the page, and go onto the next page, unless
         * the failed page is the first page.
         */
        $journals = $esi->page($this->page)
                        ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                            'corporation_id' => $corpId,
                            'division' => $this->division,
                        ]);

        //Decode the json data, and return it as an array
        $wallet = json_decode($journals->raw, true);

        //Foreach journal entry, add the journal entry to the table
        foreach($wallet as $entry) {                 
            //See if we find the entry id in the database already
            $found = AllianceWalletJournal::where([
                'id' => $entry['id'],
            ])->count();

            if($found == 0) {
                $awj = new AllianceWalletJournal;
                $awj->id = $entry['id'];
                $awj->corporation_id = $corpId;
                $awj->division = $this->division;
                if(isset($entry['amount'])) {
                    $awj->amount = $entry['amount'];
                }
                if(isset($entry['balance'])) {
                    $awj->balance = $entry['balance'];
                }
                if(isset($entry['context_id'])) {
                    $awj->context_id = $entry['context_id'];
                }
                if(isset($entry['date'])) {
                    $awj->date = $esiHelper->DecodeDate($entry['date']);
                }
                if(isset($entry['description'])) {
                    $awj->description = $entry['description'];
                }
                if(isset($entry['first_party_id'])) {
                    $awj->first_party_id = $entry['first_party_id'];
                }
                if(isset($entry['reason'])) {
                    $awj->reason = $entry['reason'];
                }
                if(isset($entry['ref_type'])) {
                    $awj->ref_type = $entry['ref_type'];
                }
                if(isset($entry['tax'])) {
                    $awj->tax = $entry['tax'];
                }
                if(isset($entry['tax_receiver_id'])) {
                    $awj->tax_receiver_id = $entry['tax_receiver_id'];
                }
                $awj->save();

            }
        }

        //Return as completed
        return 0;
    }

    /**
     * The job failed to process
     * @param Exception $exception
     * @return void
     */
    public function failed($exception) {
        if(!exception instanceof RequestFailedException) {
            //If not a failure due to ESI, then log it.  Otherwise,
            //deduce why the exception occurred.
            Log::critical($exception);
        }

        if ((is_object($exception->getEsiResponse()) && (stristr($exception->getEsiResponse()->error, 'Too many errors') || stristr($exception->getEsiResponse()->error, 'This software has exceeded the error limit for ESI'))) || 
           (is_string($exception->getEsiResponse()) && (stristr($exception->getEsiResponse(), 'Too many errors') || stristr($exception->getEsiResponse(), 'This software has exceeded the error limit for ESI')))) {
            
            //We have hit the error rate limiter, wait 120 seconds before releasing the job back into the queue.
            Log::info('UpdateAllianceWalletJournalPage has hit the error rate limiter.  Releasing the job back into the wild in 2 minutes.');
            $this->release(120);
        }  else {
            $errorCode = $exception->getEsiResponse()->getErrorCode();

            switch($errorCode) {
                case 400:  //Bad Request
                    Log::critical("Bad request has occurred in UpdateAllianceWalletJournalPage.  Job has been discarded");
                    break;
                case 401:  //Unauthorized Request
                    Log::critical("Unauthorized request has occurred in UpdateAllianceWalletJournalPage at " . Carbon::now()->toDateTimeString() . ".\r\nCancelling the job.");
                    $this->delete();
                    break;
                case 403:  //Forbidden
                    Log::critical("UpdateAllianceWalletJournalPage has incurred a forbidden error.  Cancelling the job.");
                    $this->delete();
                    break;
                case 420:  //Error Limited
                    Log::warning("Error rate limit occurred in UpdateAllianceWalletJournalPage.  Restarting job in 120 seconds.");
                    $this->release(120);
                    break;
                case 500:  //Internal Server Error
                    Log::critical("Internal Server Error for ESI in UpdateAllianceWalletJournalPage.  Attempting a restart in 120 seconds.");
                    $this->release(120);
                    break;
                case 503:  //Service Unavailable
                    Log::critical("Service Unavailabe for ESI in UpdateAllianceWalletJournalPage.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 504:  //Gateway Timeout
                    Log::critical("Gateway timeout in UpdateAllianceWalletJournalPage.  Releasing the job back to the queue in 30 seconds.");
                    $this->release(30);
                    break;
                case 201:
                    //Good response code
                    break;
                //If no code is given, then log and break out of switch.
                default:
                    Log::warning("No response code received from esi call in UpdateAllianceWalletJournalPage.\r\n");
                    $this->delete();
                    break;
            }
        }
    }

    /**
     * Set the tags for Horzion
     * 
     * @var array
     */
    public function tags() {
        return ['UpdateAllianceWalletJournalPage', 'Finances'];
    }
}
