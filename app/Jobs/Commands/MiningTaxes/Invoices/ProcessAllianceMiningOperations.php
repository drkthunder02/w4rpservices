<?php

namespace App\Jobs\Jobs\Commands\MiningTaxes\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;
use Log;

//Models
use App\Models\MiningTax\MiningOperation;
use App\Models\MiningTax\Ledger;

class ProcessAllianceMiningOperations implements ShouldQueue
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Set job parameters
        $this->connection = 'redis';
        $this->onQueue('miningtaxes');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = MiningOperation::where([
            'processed' => 'No',
        ])->where('operation_date', '<=', Carbon::now())
          ->count()

        if($count > 0) {
            $operations = MiningOperation::where([
                'processed' => 'No',
            ])->where('operation_date', '<=', Carbon::now())
              ->get();
    
            foreach($operations as $operation) {
                $ledgers = Ledger::where([
                    'observer_id' => $operation->structure_id,
                    'invoiced' => 'No',
                ])->get();
    
                foreach($ledgers as $ledger) {
                    Ledger::where([
                        'observer_id' => $operation->structure_id,
                        'invoiced' => 'No',
                    ])->update([
                        'invoiced' => 'Yes',
                        'invoice_id' => 'Mining Op ',
                    ]);
                }
    
                MiningOperation::where([
                    'id' => $operation->id,
                ])->update([
                    'processed' => 'Yes',
                    'processed_on' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Set the tags for Horzion
     * 
     * @var array
     */
    public function tags() {
        return ['ProcessAllianceMiningOperations', 'MiningTaxes', 'MiningOperations'];
    }
}
