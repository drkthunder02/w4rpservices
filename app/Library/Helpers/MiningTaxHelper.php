<?php

namespace App\Library\Helpers;

// Internal Libraries
use App\Models\MiningTax\Invoice;
// Application Library
// Models
use App\Models\MiningTax\Ledger;
use Illuminate\Support\Collection;

// Jobs

class MiningTaxHelper
{
    /**
     * Private variables
     */

    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * Get the ledgers for a certain character and send back as a collection
     *
     * @var
     *
     * @return collection $ledgers
     */
    public function GetLedgers(int $charId)
    {
        $ledgers = new Collection;

        $rowCount = Ledger::where([
            'character_id' => $charId,
            'invoiced' => 'No',
        ])->count();

        if ($rowCount > 0) {
            $rows = Ledger::where([
                'character_id' => $charId,
                'invoiced' => 'No',
            ])->get()->toArray();

            foreach ($rows as $row) {
                $ledgers->push($row);
            }
        }

        return $ledgers;
    }

    /**
     * Create the invoice and mail it
     *
     * @var int
     * @var collection
     * @var int
     */
    public function MailMiningInvoice(int $charId, collection $ledgers, int &$mailDelay) {}
}
