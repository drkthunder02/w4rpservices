<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Library\Helpers\LookupHelper;

class TestController extends Controller
{
    public function displayCharTest() {
        $lookup = new LookupHelper;

        $config = config('esi');

        $char = $lookup->GetCharacterInfo($config['primary']);

        return view('test.char.display')->with('char', $char);
    }

    public function CharacterLookupTest(Request $request) {
        
    }

    public function DebugMiningTaxes($invoiceId) {
        $invoice = array();
        $ores = array();
        $totalPrice = 0.00;
        $body = null;
        $lookup = new LookupHelper;
        $config = config('esi');

        $rows = Ledger::where([
            'invoice_id' => $invoiceId,
        ])->get()->toArray();

        //Taly up the item composition from each row and multiply by the quantity
        if(sizeof($rows) > 0) {
            foreach($rows as $row) {
                if(!isset($ores[$row['type_id']])) {
                    $ores[$row['type_id']] = 0;
                }
                $ores[$row['type_id']] = $ores[$row['type_id']] + $row['quantity'];

                //Add up the total price from the ledger rows for the report later
                $totalPrice = $totalPrice + $row['amount'];
            }

            //Reduce the total price by the take percentage
            $invoiceAmount = $totalPrice * $config['mining_tax'];
            $invoiceAmount = round($invoiceAmount, 2);
            
            //Get the character name from the character id
            $charName = $lookup->CharacterIdToName($charId);

            //Generate a unique invoice id
            $invoiceId = "M" . uniqid();
            //Set the due date of the invoice
            $dateDue = Carbon::now()->addDays(7);
            $invoiceDate = Carbon::now();

            //Format the mining tax into a human readable number
            $numberMiningTax = number_format(($config['mining_tax'] * 100.00), 2, ".", ",");
        }

        return view('test.miningtax.display')->with('rows', $rows)
                                             ->with('ores', $ores)
                                             ->with('totalPrice', $totalPrice)
                                             ->with('invoiceAmount', $invoiceAmount)
                                             ->with('charName', $charName)
                                             ->with('invoiceId', $invoiceId)
                                             ->with('dateDue' $dateDue)
                                             ->with('invoiceDate', $invoiceDate)
                                             ->with('numberMiningTax', $numberMiningTax);
    }
}
