<?php

namespace App\Console\Commands\MiningTaxes;

//Internal Library
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

//Application Library
use Commands\Library\CommandHelper;
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Ledger;
use App\Models\User\UserAlt;
use App\Models\User\User;

//Jobs
use App\Jobs\Commands\Eve\ProcessSendEveMailJob;

class MiningTaxesInvoicesNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MiningTax:InvoiceNew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mining Taxes Invoice Command';

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
        //Declare variables
        $lookup = new LookupHelper;
        $config = config('esi');
        $task = new CommandHelper('MiningTaxesInvoicesNew');
        $mainsAlts = array();
        $mailDelay = 15;
        //Set the task as started
        $task->SetStartStatus();

        //Get the characters for each non-invoiced ledger entry
        $charIds = Ledger::where([
            'invoiced' => 'No',
                       ])->distinct('character_id')
                         ->pluck('character_id');
        
        //If character ids are null, then there are no ledgers to process.
        if($charIds->count() == 0) {
            //Set the task status as done and Log the issue
            $task->SetStopStatus();
            Log::warning("No characters found to send invoices to in MiningTaxesInvoices Command.");
            return 0;
        }

        /**
         * From the list of character ids, create an array of mains and alts to group together.
         */
        foreach($charIds as $charId) {
            $invoice = array();
            $ores = array();
            $totalPrice = 0.00;
            $body = null;
            $mainId = null;

            //Determine if this character is an alt of someone else
            $foundAlt = UserAlt::where([
                'character_id' => $charId,
            ])->get();
            
            //If we found an alt, then take the main's id and use it for the next set of calculations
            if($foundAlt->count() > 0) {
                $mainId = $foundAlt->main_id;
            } else {
                //If we didn't find an alt, then we assume this is the main character's id
                $mainId = $charId;
            }

            //Build a character Id list for the characters before processing the ledgers.
            $alts = UserAlt::where([
                'main_id' => $mainId,
            ])->get();

            //First check to see if the main character had a mining ledger from a corp moon
            $mainIdLedgerFound = Ledger::where([
                'character_id' => $mainId,
                'invoiced' => 'No',
            ])->count();

            //If there are mining ledgers, then this is the character want to send the invoice to once everything has been tallied up.
            if($mainIdLedgerFound > 0) {

            } else {
                
            }

            
        }

        //Set the task as stopped
        $task->SetStopStatus();

        return 0;
    }
}
