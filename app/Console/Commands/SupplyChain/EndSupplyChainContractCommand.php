<?php

namespace App\Console\Commands\SupplyChain;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Models
use App\Models\Contracts\SupplyChainContract;

//Job
use App\Jobs\Commands\SupplyChain\EndSupplyChainContractJob;

class EndSupplyChainContractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:supplychain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and ends any supply chain contracts needs to be closed.';

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
        $today = Carbon::now();

        //Get the supply chain contracts which are open, but need to be closed.
        $contracts = SupplyChainContract::where([
            'state' => 'open',
        ])->where('end_date', '>', $today)->get();

        //Create jobs to complete each contract
        foreach($contracts as $contract) {
            EndSupplyChainContractJob::dispatch($contract)->onQueue('default');
        }
    }
}
