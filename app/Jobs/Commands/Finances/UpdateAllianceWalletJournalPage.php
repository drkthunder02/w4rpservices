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
        try {
            $journals = $esi->page($this->page)
                            ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                                'corporation_id' => $corpId,
                                'division' => $this->division,
                            ]);
        } catch(RequestFailedException $e) {
            Log::warning('Failed to get wallet journal page ' . $currentPage . ' for character id: ' . $charId);
            Log::warning($e);
            $this->delete();
        }

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
     * Set the tags for Horzion
     * 
     * @var array
     */
    public function tags() {
        return ['UpdateAllianceWalletJournalPage', 'Finances'];
    }
}
