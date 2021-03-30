<?php

namespace App\Jobs\Commands\MiningTaxes;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Log;

//Library
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\MiningTaxes\Invoice;
use App\Models\MiningTaxes\Payment;
use App\Models\Finances\PlayerDonationJournal;

class ProcessMiningTaxesPaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the variables we will need
        $looup = new LookupHelper;
        $currentTime = Carbon::now();

        //Get the outstanding invoices
        $outstanding = Invoice::where([
            'status' => 'Pending',
        ])->get();

        //Use the player donation journal from finances to see if the invoice_id is present
        //as a reason
        foreach($outstanding as $invoice) {
            //See if we have a reason with the correct uniqid from the player donation journal
            $found = PlayerDonationJournal::where([
                'reason' => "MMT: " . $invoice->invoice_id,
            ])->count();

            //If we have received the invoice, then mark the invoice as paid
            if($count > 0) {
                //If we have the count, then grab the journal entry in order to do some things with it
                $journal = PlayerDonationJournal::where([
                    'reason' => "MMT: " . $invoice->invoice_id,
                ])->first();

                //If the bill is paid on time, then update the invoice as such
                if($currentTime->lessThanOrEqualTo($journal->inserted_at)) {
                    Invoice::where([
                        'invoice_id' => $invoice->invoice_id,
                    ])->update([
                        'status' => 'Paid',
                    ]);
                }

                if($currentTime->greaterThan($journal->inserted_at)) {
                    Invoice::where([
                        'invoice_id' => $invoice->invoice_id,
                    ])->update([
                        'status' => 'Paid Late',
                    ]);
                }
            }
        }

    }
}
