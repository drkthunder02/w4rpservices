<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;

//Jobs
use App\Jobs\ProcessWalletJournalJob;

//Models
use App\Models\Jobs\JobProcessWalletJournal;

class SovBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:SovBills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the holding corps sov bills from wallet 6.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sovBill = new SovBillExpenses;
        $esiHelper = new Esi;
        $finance = new FinanceHelper();

        //Create the command helper container
        $task = new CommandHelper('SovBills');

        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();
        

        //Get the esi configuration
        $config = config('esi');
        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Create an ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);
        $esi->setVersion('v4');

        $token = $esiHelper->GetRefreshToken($config['primary']);
        if($token == null) {
            return null;
        }

        //Reference to see if the character is in our look up table for corporations and characters
        $char = $lookup->GetCharacterInfo($charId);
        $corpId = $char->corporation_id;

        //Get the total pages for the journal for the sov bills from the holding corporation
        $pages = $finance->GetJournalPageCount(6, $config['primary']);

        //Dispatch a job for each page to process
        //for($i = 1; $i <= $pages; $i++) {
        //    $job = new JobProcessWalletJournal;
        //    $job->division = 6;
        //    $job->charId = $config['primary'];
        //    $job->page = $i;
        //    ProcessWalletJournalJob::dispatch($job)->onQueue('journal');
        //}

        //Try to figure it out from the command itself.
        for($i = 1; $i <= $pages; $i++) {
            printf("Getting page: " . $i . "\n");

            try {
                $journals = $esi->page($page)
                                ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                    'corporation_id' => $corpId,
                    'division'  => $division,
                ]);
            } catch(RequestFailedException $e) {
                return null;
            }

            //Decode the wallet from json into an array
            $wallet = json_decode($journals->raw, true);
            dd($wallet);
            foreach($wallet as $entry) {
                if($entry['amount'] > 0) {
                    if($entry['ref_type'] == 'infrastructure_hub_maintenance' && $entry['first_party_id'] == 98287666) {
                        $sovBill->InsertSovBillExpense($entry, $corpId, $division);
                    }
                }
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
