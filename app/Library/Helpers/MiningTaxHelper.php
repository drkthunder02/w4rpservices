<?php

namespace App\Library\Helpers;

//Internal Libraries
use Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

//Application Library
use App\Library\Helpers\LookupHelper;

//Models
use App\Models\MiningTax\Invoice;
use App\Models\MiningTax\Ledger;

//Jobs
use App\Jobs\Commands\Eve\SendEveMail;

class MiningTaxHelper {
    /**
     * Private variables
     */

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Get the ledgers for a certain character and send back as a collection
     * 
     * @var $charId
     * @return collection $ledgers
     */
    public function GetLedgers(int $charId) {
        $ledgers = new Collection;

        $rowCount = Ledger::where([
            'character_id' => $charId,
            'invoiced' => 'No',
        ])->count();

        if($rowCount > 0) {
            $rows = Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->get()->toArray();

            foreach($rows as $row) {
                $ledgers->push($row);
            }
        }

        return $ledgers;
    }

    /**
     * Create the invoice and mail it
     * 
     * @var int $charId
     * @var collection $ledgers
     * @var int $mailDelay
     * 
     */
    public function MailMiningInvoice(int $charId, collection $ledgers, int &$mailDelay) {
        
    } 
}