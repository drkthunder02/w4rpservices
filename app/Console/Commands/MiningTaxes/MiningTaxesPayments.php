<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Commands\Library\CommandHelper;
use App\Library\Helpers\LookupHelper;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

//Models
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Payment;
use App\Models\Finances\PlayerDonationJournal;

//Jobs
use App\Jobs\Commands\MiningTaxes\ProcessMiningTaxesPaymentsJob;

class MiningTaxesPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:Payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process mining tax payments';

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
     * @return int
     */
    public function handle()
    {
        //Create the command helper container
        $task = new CommandHelper('MiningTaxesPayments');
        //Set the task as started
        $task->SetStartStatus();

       //Declare variables for the function
       $lookup = new LookupHelper;
       $currentTime = Carbon::now();
       $config = config('esi');
       $esiHelper = new Esi;

       //Check for the esi scope
       if(!$esiHelper->HaveEsiScope($config['primary'], 'esi-contracts.read_corporation_contracts.v1')) {
           Log::critical('Esi scopes were not found for MiningTaxesPayments');
           print("Esi scope not found.");
           return;
       }

       //Get the refresh token
       $refreshToken = $esiHelper->GetRefreshToken($config['primary']);
       //Get the esi variable
       $esi = $esiHelper->SetupEsiAuthentication($refreshToken);

       //Get all of the contracts from esi
       try {
           $response = $esi->invoke('get', '/corporations/{corporation_id}/contracts/', [
                'corporation_id' => $config['corporation'],
           ]);
       } catch(RequestFailedException $e) {
           Log::critical("Failed to get contrcts in MiningTaxesPayments command.");
           Log::critical($e);
           dd($e);
       }

       //Decode the contracts from the json response
       $contracts = json_decode($response->raw, false);

       //Get the outstanding invoices
       $outstanding = Invoice::where([
           'status' => 'Pending',
       ])->get();

       //Use the player donation and the journal in order to check over the pending contracts to attempt
       //to pay the contract
       foreach($outstanding as $invoice) {
            //See if we have a reason with the correct uniqid from the player donation journal
            $found = PlayerDonationJournal::where([
               'reason' => "MMT: " . $invoice->invoice_id,
            ])->count();
            
            if($found == 1) {
                //If the bill is paid on time, then update the invoice as such
                if($currentTime->lessThanOrEqualTo($invoice->due_date)) {
                    Invoice::where([
                        'invoice_id' => $invoice->invoice_id,
                    ])->update([
                        'status' => 'Paid',
                    ]);
                }

                //If the bill is paid late, then update the invoice as such
                if($currentTime->greaterThan($invoice->due_date)) {
                    Invoice::where([
                        'invoice_id' => $invoice->invoice_id,
                    ])->update([
                        'status' => 'Paid Late',
                    ]);
                }
            } else {
                //If we didn't found a journal entry, then we shall check the contracts for a correct entry
                foreach($contracts as $contract) {
                    if(($contract->title == ("MMT: " . $invoice->invoice_id)) && ($currentTime->lessThanOrEqualTo($invoice->due_date))) {
                        Invoice::where([
                            'invoice_id' => $invoice->invoice_id,
                        ])->update([
                            'stauts' => 'Paid'
                        ]);
                    }

                    if(($contract->title == ("MMT: " . $invoice_id)) && ($currentTime->greaterThan($invoice->due_date))) {
                        Invoice::where([
                            'invoice_id' => $invoice->invoice_id,
                        ])->update([
                            'status' => 'Paid Late',
                        ]);
                    }
                }
            }
       }

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
