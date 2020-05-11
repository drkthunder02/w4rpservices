<?php

namespace App\Console\Commands\Finances;

use Illuminate\Console\Command;
use Log;

use Commands\Library\CommandHelper;
use App\Library\Finances\Helper\FinanceHelper;

//Jobs
use App\Jobs\ProcessWalletJournalJob;

//Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;

//Library
use App\Library\Esi\Esi;
use App\Library\Finances\AllianceMarketTax;
use App\Library\Finances\CorpMarketTax;
use App\Library\Finances\MarketTax;
use App\Library\Finances\PlayerDonation;
use App\Library\Finances\ReprocessingTax;
use App\Library\Finances\JumpBridgeTax;
use App\Library\Finances\StructureIndustryTax;
use App\Library\Finances\OfficeFee;
use App\Library\Finances\PlanetProductionTax;
use App\Library\Finances\PISale;
use App\Library\Lookups\LookupHelper;
use App\Library\Finances\SovBillExpenses;

//Seat Stuff
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;

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
        $lookup = new LookupHelper;

        //Create the command helper container
        $task = new CommandHelper('SovBills');

        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();
        

        //Get the esi configuration
        $config = config('esi');
        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        $token = $esiHelper->GetRefreshToken($config['primary']);
        if($token == null) {
            return null;
        }

        //Create an ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);
        $esi->setVersion('v4');

        //Reference to see if the character is in our look up table for corporations and characters
        $char = $lookup->GetCharacterInfo($config['primary']);
        $corpId = $char->corporation_id;

        //Get the total pages for the journal for the sov bills from the holding corporation
        $pages = $finance->GetJournalPageCount(6, $config['primary']);

        //Try to figure it out from the command itself.
        for($i = 1; $i <= $pages; $i++) {
            printf("Getting page: " . $i . "\n");

            try {
                $journals = $esi->page($i)
                                ->invoke('get', '/corporations/{corporation_id}/wallets/{division}/journal/', [
                    'corporation_id' => $corpId,
                    'division'  => 6,
                ]);
            } catch(RequestFailedException $e) {
                return null;
            }

            //Decode the wallet from json into an array
            $wallet = json_decode($journals->raw, true);
            dd($wallet);
            foreach($wallet as $entry) {
                if($entry['ref_type'] == 'infrastructure_hub_maintenance' && $entry['first_party_id'] == 98287666) {
                    $sovBill->InsertSovBillExpense($entry, $corpId, $division);
                }
            }
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
