<?php

namespace App\Jobs\Commands\SupplyChain;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Log;

//Library
use App\Library\Lookups\LookupHelper;

//Models
use App\Models\Contracts\SupplyChainBid;
use App\Models\Contracts\SupplyChainContract;

//Jobs
use App\Jobs\ProcessSendEveMailJob;

class EndSupplyChainContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 1200;

    /**
     * Retries
     * 
     * @var int
     */
    public $retries = 3;

    /**
     * Private Variables
     */
    private $contractId;
    private $issuerId;
    private $issuerName;
    private $title;
    private $endDate;
    private $deliveryBy;
    private $body;
    private $state;
    private $finalCost;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SupplyChainContract $contract)
    {
        //Set the queue connection up
        $this->connection = 'redis';

        //Set the variables
        $contractId = $contract->contract_id;
        $issuerId = $contract->issuer_id;
        $issuerName = $contract->issuer_name;
        $title = $contract->title;
        $endDate = $contract->end_date;
        $deliveryBy = $contract->delivery_by;
        $body = $contract->body;
        $state = $contract->state;
        $finalCost = $contract->final_cost;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare variables
        $bidId = null;
        $bidAmount = null;

        //Get all of the bids from the contract
        $bids = SupplyChainBids::where([
            'contract_id' => $contractId,
        ])->get();

        //Loop through the bids and find the lowest bid
        foreach($bids as $bid) {
            if($bidId == null) {
                $bidId = $bid->id;
                $bidAmount = $bid->bid_amount;
            } else {
                if($bid->bid_amount < $bidAmount) {
                    $bidId = $bid->id;
                    $bidAmount = $bid->bid_amount;
                }
            }
        }

        //Clean up the bids and update the contract with the winning bid
        SupplyChainContract::where([
            'contract_id' => $this->contractId,
        ])->update([
            'final_cost' => $bidAmount,
            'winning_bid_id' => $bidId,
        ]);
    }
}
