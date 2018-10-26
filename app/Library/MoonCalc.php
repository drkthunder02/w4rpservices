<?php
/* 
 *  W4RP Services
 *  GNU Public License
 */

namespace App\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Config;
use App\Moon;
use App\Price;
use App\ItemComposition;

class MoonCalc {

    public function SpatialMoons($firstOre, $firstQuan, $secondOre, $secondQuan, $thirdOre, $thirdQuan, $fourthOre, $fourthQuan) {
        //Always assume a 1 month pull which equates to 5.55m3 per second or 2,592,000 seconds
        //Total pull size is 14,385,600 m3
        $totalPull = 5.55 * (3600.00 * 24.00 * 30.00);
        //Get the configuration for pricing calculations
        $config = DB::table('Config')->get();
        if($firstQuan >= 1.00) {
            $firstPerc = $this->ConvertToPercentage($firstQuan);
        } else {
            $firstPerc = $firstQuan;
        }
        if($secondQuan >= 1.00) {
            $secondPerc = $this->ConvertToPercentage($secondQuan);
        } else {
            $secondPerc = $secondQuan;
        }
        if($thirdQuan >= 1.00) {
            $thirdPerc = $this->ConvertToPercentage($thirdQuan);
        } else {
            $thirdPerc = $thirdQuan;
        }
        if($fourthQuan >= 1.00) {
            $fourthPerc = $this->ConvertToPercentage($fourthQuan);
        } else {
            $fourthPerc = $fourthQuan;
        }
        if($firstOre != "None") {
            $firstTotal = $this->CalcPrice($firstOre, $firstPerc);
        } else {
            $firstTotal = 0.00;
        }
        if($secondOre != "None") {
            $secondTotal = $this->CalcPrice($secondOre, $secondPerc);
        } else {
            $secondTotal = 0.00;
        }
        if($thirdOre != "None") {
            $thirdTotal = $this->CalcPrice($thirdOre, $thirdPerc);
        } else {
            $thirdTotal = 0.00;
        }
        if($fourthOre != "None") {
            $fourthTotal = $this->CalcPrice($fourthOre, $fourthPerc);
        } else {
            $fourthTotal = 0.00;
        }
        //Calculate the total to price to be mined in one month
        $totalPriceMined = $firstTotal + $secondTotal + $thirdTotal + $fourthTotal;
        //Calculate the rental price.  Refined rate is already included in the price from rental composition
        $rentalPrice = $totalPriceMined * ($config->RentalTax / 100.00);
        //Format the rental price to the appropriate number
        $rentalPrice = number_format($rentalPrice, "2", ".", ",");
       
        //Return the rental price to the caller
        return $rentalPrice;
    }

    public function FetchNewPrices() {
        $ItemIDs = array(
            "Tritanium" => 34,
            "Pyerite" => 35,
            "Mexallon" => 36,
            "Isogen" => 37,
            "Nocxium" => 38,
            "Zydrine" => 39,
            "Megacyte" => 40,
            "Morphite" => 11399,
            "HeliumIsotopes" => 16274,
            "NitrogenIsotopes" => 17888,
            "OxygenIsotopes" => 17887,
            "HydrogenIsotopes" => 17889,
            "LiquidOzone" => 16273,
            "HeavyWater" => 16272,
            "StrontiumClathrates" => 16275,
            "AtmosphericGases" => 16634,
            "EvaporiteDeposits" => 16635,
            "Hydrocarbons" => 16633,
            "Silicates" => 16636,
            "Cobalt" => 16640,
            "Scandium" => 16639,
            "Titanium" => 16638,
            "Tungsten" => 16637,
            "Cadmium" => 16643,
            "Platinum" => 16644,
            "Vanadium" => 16642,
            "Chromium" => 16641,
            "Technetium" => 16649,
            "Hafnium" => 16648,
            "Caesium" => 16647,
            "Mercury" => 16646,
            "Dysprosium" => 16650,
            "Neodymium" => 16651,
            "Promethium" => 16652,
            "Thulium" => 16653,
        );
        $time = time();
        $item = array();
        //Get the json data for each ItemId from https://market.fuzzwork.co.uk/api/
        //Base url is https://market.fuzzwork.co.uk/aggregates/?region=10000002&types=34
        //Going to use curl for these requests
        foreach($ItemIDs as $key => $value) {
            $client = new Client(['base_uri' => 'https://market.fuzzwork.co.uk/aggregates/']);
            $uri = '?region=10000002&types=' . $value;
            $result = $client->request('GET', $uri);
            $item = json_decode($result->getBody(), true);
            
            DB::table('Prices')->insert([
                'Name' => $key,
                'ItemId' => $value,
                'Price' => $item[$value]['sell']['median'],
                //'Price' => $item->value->sell->median,
                'Time' => $time
            ]);
        }

        $this->UpdateItemPricing();
        //Close the database connection
        DBClose($db);
    }

    private function UpdateItemPricing() {

        //Get the configuration from the config table
        $config = DB::table('Config')->first();
        //Calculate refine rate
        $refineRate = $config->RefineRate / 100.00;
        //Calculate the current time
        $time = time();
        //Get the max time from the database
        $maxTime = DB::table('Prices')->where('ItemId', 34)->max('Time');
        //Get the price of the basic minerals
        $tritaniumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [34, $maxTime]);
        $tritanium = DB::select( DB::raw('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time'), array('id' => 34, 'time' => $maxTime));
        $pyeritePrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [35, $maxTime]);
        $mexallonPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [36, $maxTime]);
        $isogenPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [37, $maxTime]);
        $nocxiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [38, $maxTime]);
        $zydrinePrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [39, $maxTime]);
        $megacytePrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [40, $maxTime]);
        $morphitePrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [11399, $maxTime]);
        $heliumIsotopesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16274, $maxTime]);
        $nitrogenIsotopesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [17888, $maxTime]);
        $oxygenIsotopesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [17887, $maxTime]);
        $hydrogenIsotopesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [17889, $maxTime]);
        $liquidOzonePrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16273, $maxTime]);
        $heavyWaterPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16272, $maxTime]);
        $strontiumClathratesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16275, $maxTime]);
        //Get the price of the moongoo
        $atmosphericGasesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16634, $maxTime]);
        $evaporiteDepositsPirce = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16635, $maxTime]);
        $hydrocarbonsPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16633, $maxTime]);
        $silicatesPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16636, $maxTime]);
        $cobaltPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16640, $maxTime]);
        $scandiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16639, $maxTime]);
        $titaniumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16638, $maxTime]);
        $tungstenPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16637, $maxTime]);
        $cadmiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16643, $maxTime]);
        $platinumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16644, $maxTime]);
        $vanadiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16642, $maxTime]);
        $chromiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16641, $maxTime]);
        $technetiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16649, $maxTime]);
        $hafniumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16648, $maxTime]);
        $caesiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16647, $maxTime]);
        $mercuryPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16646, $maxTime]);
        $dysprosiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16650, $maxTime]);
        $neodymiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16651, $maxTime]);
        $promethiumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16652, $maxTime]);
        $thuliumPrice = DB::select('SELECT Price FROM Prices WHERE ItemId = ? AND Time = ?', [16653, $maxTime]);
        //Get the item compositions
        $items = DB::select('SELECT Name,ItemId FROM ItemComposition');
        //Go through each of the items and update the price
        foreach($items as $item) {
            //Get the item composition
            $composition = DB::select('SELECT * FROM ItemComposition WHERE ItemId = ?', [$item->ItemId]);
            //Calculate the Batch Price
            $batchPrice = ( ($composition[0]->Tritanium * $tritaniumPrice[0]->Price) +
                            ($composition[0]->Pyerite * $pyeritePrice[0]->Price) +
                            ($composition[0]->Mexallon * $mexallonPrice[0]->Price) +
                            ($composition[0]->Isogen * $isogenPrice[0]->Price) +
                            ($composition[0]->Nocxium * $nocxiumPrice[0]->Price) +
                            ($composition[0]->Zydrine * $zydrinePrice[0]->Price) +
                            ($composition[0]->Megacyte * $megacytePrice[0]->Price) + 
                            ($composition[0]->Morphite * $morphitePrice[0]->Price) +
                            ($composition[0]->HeavyWater * $heavyWaterPrice[0]->Price) +
                            ($composition[0]->LiquidOzone * $liquidOzonePrice[0]->Price) +
                            ($composition[0]->NitrogenIsotopes * $nitrogenIsotopesPrice[0]->Price) +
                            ($composition[0]->HeliumIsotopes * $heliumIsotopesPrice[0]->Price) + 
                            ($composition[0]->HydrogenIsotopes * $hydrogenIsotopesPrice[0]->Price) +
                            ($composition[0]->OxygenIsotopes * $oxygenIsotopesPrice[0]->Price) +
                            ($composition[0]->StrontiumClathrates * $strontiumClathratesPrice[0]->Price) +
                            ($composition[0]->AtmosphericGases * $atmosphericGasesPrice[0]->Price) +
                            ($composition[0]->EvaporiteDeposits * $evaporiteDepositsPirce[0]->Price) +
                            ($composition[0]->Hydrocarbons * $hydrocarbonsPrice[0]->Price) +
                            ($composition[0]->Silicates * $silicatesPrice[0]->Price) +
                            ($composition[0]->Cobalt * $cobaltPrice[0]->Price) +
                            ($composition[0]->Scandium * $scandiumPrice[0]->Price) +
                            ($composition[0]->Titanium * $titaniumPrice[0]->Price) +
                            ($composition[0]->Tungsten * $tungstenPrice[0]->Price) +
                            ($composition[0]->Cadmium * $cadmiumPrice[0]->Price) +
                            ($composition[0]->Platinum * $platinumPrice[0]->Price) +
                            ($composition[0]->Vanadium * $vanadiumPrice[0]->Price) +
                            ($composition[0]->Chromium * $chromiumPrice[0]->Price)+
                            ($composition[0]->Technetium * $technetiumPrice[0]->Price) +
                            ($composition[0]->Hafnium * $hafniumPrice[0]->Price) +
                            ($composition[0]->Caesium * $caesiumPrice[0]->Price) +
                            ($composition[0]->Mercury * $mercuryPrice[0]->Price) +
                            ($composition[0]->Dysprosium * $dysprosiumPrice[0]->Price) +
                            ($composition[0]->Neodymium * $neodymiumPrice[0]->Price) + 
                            ($composition[0]->Promethium * $promethiumPrice[0]->Price) +
                            ($composition[0]->Thulium * $thuliumPrice[0]->Price));
            //Calculate the batch price with the refine rate included
            //Batch Price is base price for everything
            $batchPrice = $batchPrice * $refineRate;
            //Calculate the unit price
            $price = $batchPrice / $composition[0]->BatchSize;
            //Calculate the m3 price
            $m3Price = $price / $composition[0]->m3Size;
            //Insert the prices into the Pricees table
            DB::table('OrePrices')->insert([
                'Name' => $composition[0]->Name,
                'ItemId' => $composition[0]->ItemId,
                'BatchPrice' => $batchPrice,
                'UnitPrice' => $price,
                'm3Price' => $m3Price,
                'Time' => $time
            ]);
        }
    }

    private function FuzzworkPrice($url) {
        //Initialize the curl request
        $ch = curl_init();
        //Set the curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //Execute the curl
        $result = curl_exec($ch);
        //Get the resultant data and decode the json request
        $data = json_decode($result, true);
        
        //Return the array of data
        return $data;
    }

    private function CalcPrice($ore, $percentage) {
        //Specify the total pull amount
        $totalPull = 5.55 * (3600.00 * 24.00 * 30.00);
        //Find the size of the asteroid from the database
        $m3Size = DB::table('ItemComposition')->where('Name', $ore)->value('m3Size');
        //Calculate the actual m3 from the total pull amount in m3 using the percentage of the ingredient
        $actualm3 = floor($percentage * $totalPull);
        //Calculate the units once we have the size and actual m3 value
        $units = floor($actualm3 / $m3Size);
        //Look up the unit price from the database
        $unitPrice = DB::table('OrePrices')->where('UnitPrice', $ore)->value('UnitPrice');
        //Calculate the total amount from the units and unit price
        $total = $units * $unitPrice;
        //Return the value
        return $total;
    }

    private function ConvertToPercentage($quantity) {
        return $quantity / 100.00;
    }
    
}