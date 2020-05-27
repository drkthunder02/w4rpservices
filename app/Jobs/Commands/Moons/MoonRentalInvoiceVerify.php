<?php

namespace App\Jobs\Commands\Moons;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library

//App Models
use App\Models\Finances\PlayerDonationJournal;
use App\Models\MoonRentals\MoonRentalInvoice;
use App\Models\MoonRentals\MoonRentalPayment;
use App\Models\MoonRentals\MoonRent;

class MoonRentalInvoiceVerify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 3600;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set the connection
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Create the date to look through
        $date = Carbon::now()->subDays(60);

        //Get the open moon rental invoices
        $unpaid = MoonRentalInvoice::where([
            'Paid' => 'No',
        ])->get();

        //If there are no unpaid invoices, then return out of the job as successful
        if($unpaid == null || $unpaid == false) {
            return;
        }

        //Foreach unpaid invoice, run through them and check if there is an entry in the player journal table to verify the invoice
        foreach($unpaid as $un) {
            //Get everything which has a description like the invoice code.
            $checkA = PlayerDonationJournal::where('description', 'like', '%' . $un->invoice_id . '%')->get();
            
            //Get the player donations and corp transfers which have the amount we are looking for
            $checkB = PlayerDonationJournal::where([
                'amount' => $un->invoice_amount,
            ])->where('date', '>=', $date)->get();

            //Check each of the checkA's for the correct description
            foreach($checkA as $check) {
                //Search for the sub string of the invoice id in the description
                $desc = strstr($check->description, $un->invoice_id);
                //If the description is found, then update the invoice.
                //Create an invoice payment
                if($desc == $un->invoice_id) {
                    //Insert a new moon rental payment once completed
                    MoonRentalPayment::insert([
                        'invoice_id' => $un->invoice_id,
                        'payment_amount' => $un->invoice_amount,
                        'reference_payment' => $check->id,
                    ]);
                    
                    //Update the invoice as paid
                    MoonRentalInvoice::where([
                        'invoice_id' => $un->invoice_id,
                    ])->update([
                        'Paid' => 'Yes',
                    ]);

                    //Increase the moon rental by another month
                    $moons = $un->rental_moons;
                    //Turns the moons into an array
                    $moons = explode(',', $moons);
                    foreach($moons as $moon) {
                        //Need to separate each moon into system, planet, and moon to update the moon rental

                    }
                }
            }

            //Check each of checkB's for the invoice amount
            foreach($checkB as $check) {

            }
        }
    }
}
