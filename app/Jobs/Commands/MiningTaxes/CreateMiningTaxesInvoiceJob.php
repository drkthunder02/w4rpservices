<?php

namespace App\Jobs\Commands\MiningTaxes;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMiningTaxesInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //Private Variables
    private $ores;
    private $totalPrices;
    private $charId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ores, $totalPrice, $charId)
    {
        $this->ores = $ores;
        $this->totalPrice = $totalPrice;
        $this->charId = $charId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
