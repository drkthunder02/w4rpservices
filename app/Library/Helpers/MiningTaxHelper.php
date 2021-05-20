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
use App\Models\User\User;
use App\Models\User\UserAlt;

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
     * Get the main character ledgers and send back as a collection
     * 
     * @var $charId
     * @return collection $ledgers
     */
    public function GetMainLedgers($charId) {
        $ledgers = new Collection;

        $rowCount = Ledger::where([
            'character_id' => $charId,
            'invoiced' => 'No',
        ])->count();

        $rows = Ledger::where([
            'character_id' => $charId,
            'invoiced' => 'No',
        ])->get()->toArry();

        if($rowCount > 0) {
            foreach($rows as $row) {
                $ledgers->push($row);
            }
        }

        return $ledgers;
    }

    /**
     * Get the alt characters ledgers and send back as a collection
     * 
     * @var array $alts
     * @return collection ledgers
     */
    public function GetAltLedgers($alts) {
        $ledgers = new Collection;


    }

    public function 
}