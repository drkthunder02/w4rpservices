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
