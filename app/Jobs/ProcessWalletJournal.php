<?php

namespace App\Jobs;

//Internal Libraries
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

//App Library
use App\Library\Finances\MarketTax;
use App\Library\Finances\PlayerDonation;
use App\Library\Finances\ReprocessingTax;
use App\Library\Finances\JumpBridgeTax;
use App\Library\Finances\StructureIndustryTax;
use App\Library\Finances\OfficeFee;
use App\Library\Finances\PlanetProductionTax;
use App\Library\Finances\PISale;
use App\Library\Lookups\LookupHelper;

//App Models
use App\Models\User\UserToCorporation;
use App\Models\Finances\CorpMarketJournal;
use App\Models\Finances\JumpBridgeJournal;
use App\Models\Finances\OfficeFeesJournal;
use App\Models\Finances\PISaleJournal;
use App\Models\Finances\PlanetProductionTaxJournal;
use App\Models\Finances\PlayerDonationJournal;
use App\Models\Finances\REprocessingTaxJournal;
use App\Models\Finances\StructureIndustryTaxJournal;

class ProcessWalletJournal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Class Variables for journals
     */
    protected $market;
    protected $reprocessing;
    protected $jb;
    protected $other;
    protected $industry;
    protected $office;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 600;

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
                'body' => $this->eveMail->body,
                'recipients' => [[
                    'recipient_id' => (int)$this->eveMail->recipient,
                    'recipient_type' => $this->eveMail->recipient_type,
                ]],
                'subject' => $this->eveMail->subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id'=> 93738489,
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }

        $this->eveMail->delete();
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
