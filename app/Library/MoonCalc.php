<?php
/* 
 *  W4RP Services
 *  GNU Public License
 */

namespace App\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class MoonCalc {
    
    public function __construct() {

    }

    public function SpatialMoons($firstOre, $firstQuan, $secondOre, $secondQuan, $thirdOre, $thirdQuan, $fourthOre, $fourthQuan, \Simplon\Mysql\Mysql $db){
        //Always assume a 1 month pull which equates to 5.55m3 per second or 2,592,000 seconds
        //Total pull size is 14,385,600 m3
        $totalPull = 5.55 * (3600.00 * 24.00 * 30.00);
        //Get the configuration for pricing calculations
        $config = $db->fetchRow('SELECT * FROM Config');
        if($firstQuan >= 1.00) {
        $firstPerc = $firstQuan / 100.00;
        } else {
        $firstPerc = $firstQuan;
        }
        if($secondQuan >= 1.00) {
        $secondPerc = $secondQuan / 100.00;
        } else {
        $secondPerc = $secondQuan;
        }
        if($thirdQuan >= 1.00) {
        $thirdPerc = $thirdQuan / 100.00;
        } else {
        $thirdPerc = $thirdQuan;
        }
        if($fourthQuan >= 1.00) {
        $fourthPerc = $fourthQuan / 100.00;
        } else {
        $fourthPerc = $fourthQuan;
        }
        if($firstOre != "None") {
            $m3Size = $db->fetchColumn('SELECT m3Size FROM ItemComposition WHERE Name= :name', array('name' => $firstOre));
            //Find the m3 value of the first ore
            $firstActualm3 = floor($firstPerc * $totalPull);
            //Calculate the units of the first ore
            $firstUnits = floor($firstActualm3 / $m3Size);
            //Get the unit price from the database
            $firstUnitPrice = $db->fetchColumn('SELECT UnitPrice  FROM OrePrices WHERE Name= :name', array('name'=> $firstOre));
            //Calculate the total price for the first ore
            $firstTotal = $firstUnits * $firstUnitPrice;
        } else {
            $firstTotal = 0.00;
        }
        if($secondOre != "None") {
            $m3Size = $db->fetchColumn('SELECT m3Size FROM ItemComposition WHERE Name= :name', array('name' => $secondOre));
            //find the m3 value of the second ore
            $secondActualm3 = floor($secondPerc * $totalPull);
            //Calculate the units of the second ore
            $secondUnits = floor($secondActualm3 / $m3Size);
            //Get the  unit price from the database
            $secondUnitPrice = $db->fetchColumn('SELECT UnitPrice FROM OrePrices WHERE Name= :name', array('name' => $secondOre));
            //calculate the total price for the second ore
            $secondTotal = $secondUnits * $secondUnitPrice;
        } else {
            $secondTotal = 0.00;
        }
        if($thirdOre != "None") {
            $m3Size = $db->fetchColumn('SELECT m3Size FROM ItemComposition WHERE Name= :name', array('name' => $thirdOre));
            //find the m3 value of the third ore
            $thirdActualm3 = floor($thirdPerc * $totalPull);
            //calculate the units of the third ore
            $thirdUnits = floor($thirdActualm3 / $m3Size);
            //Get the unit price from the database
            $thirdUnitPrice = $db->fetchColumn('SELECT UnitPrice FROM OrePrices WHERE Name= :name', array('name' => $thirdOre));
            //calculate the total price for the third ore
            $thirdTotal = $thirdUnits * $thirdUnitPrice;
        } else {
            $thirdTotal = 0.00;
        }
        if($fourthOre != "None") {
            $m3Size = $db->fetchColumn('SELECT m3Size FROM ItemComposition WHERE Name= :name', array('name' => $fourthOre));
            //Find the m3 value of the fourth ore
            $fourthActualm3 = floor($fourthPerc * $totalPull);
            //Calculate the units of the fourth ore
            $fourthUnits = floor($fourthActualm3 / $m3Size);
            //Get the unit price from the database
            $fourthUnitPrice = $db->fetchColumn('SELECT UnitPrice FROM OrePrices WHERE Name= :name', array('name' => $fourthOre));
            //calculate the total price for the fourth ore
            $fourthTotal = $fourthUnits * $fourthUnitPrice;
        } else {
            $fourthTotal = 0.00;
        }
        //Calculate the total to price to be mined in one month
        $totalPriceMined = $firstTotal + $secondTotal + $thirdTotal + $fourthTotal;
        //Calculate the rental price.  Refined rate is already included in the price from rental composition
        $rentalPrice = $totalPriceMined * ($config['RentalTax'] / 100.00);
        //Format the rental price to the appropriate number
        $rentalPrice = number_format($rentalPrice, "2", ".", ",");
       
        //Return the rental price to the caller
        return $rentalPrice;
    }

    public function UpdateItemPricing() {
    
        if(php_sapi_name() != 'cli') {
            $browser = true;
            printf("Running price update from browser.<br>");
        } else {
            $browser = false;
            printf("Running price update from command line.\n");
        }
        $db = DBOpen();
        //Get the configuration from the config table
        $config = $db->fetchRow('SELECT * FROM Config');
        //Calculate refine rate
        $refineRate = $config['RefineRate'] / 100.00;
        //Calculate the current time
        $time = time();
        //Get the max time from the database
        $maxTime = $db->fetchColumn('SELECT MAX(Time) FROM Prices WHERE ItemId= :id', array('id' => 34));
        //Get the price of the basic minerals
        $tritaniumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 34, 'time' => $maxTime));
        $pyeritePrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 35, 'time' => $maxTime));
        $mexallonPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 36, 'time' => $maxTime));
        $isogenPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 37, 'time' => $maxTime));
        $nocxiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 38, 'time' => $maxTime));
        $zydrinePrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 39, 'time' => $maxTime));
        $megacytePrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 40, 'time' => $maxTime));
        $morphitePrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 11399, 'time' => $maxTime));
        $heliumIsotopesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16274, 'time' => $maxTime));
        $nitrogenIsotopesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 17888, 'time' => $maxTime));
        $oxygenIsotopesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 17887, 'time' => $maxTime));
        $hydrogenIsotopesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 17889, 'time' => $maxTime));
        $liquidOzonePrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16273, 'time' => $maxTime));
        $heavyWaterPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16272, 'time' => $maxTime));
        $strontiumClathratesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16275, 'time' => $maxTime));
        //Get the price of the moongoo
        $atmosphericGasesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16634, 'time' => $maxTime));
        $evaporiteDepositsPirce = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16635, 'time' => $maxTime));
        $hydrocarbonsPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16633, 'time' => $maxTime));
        $silicatesPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16636, 'time' => $maxTime));
        $cobaltPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16640, 'time' => $maxTime));
        $scandiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16639, 'time' => $maxTime));
        $titaniumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16638, 'time' => $maxTime));
        $tungstenPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16637, 'time' => $maxTime));
        $cadmiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16643, 'time' => $maxTime));
        $platinumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16644, 'time' => $maxTime));
        $vanadiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16642, 'time' => $maxTime));
        $chromiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16641, 'time' => $maxTime));
        $technetiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16649, 'time' => $maxTime));
        $hafniumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16648, 'time' => $maxTime));
        $caesiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16647, 'time' => $maxTime));
        $mercuryPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16646, 'time' => $maxTime));
        $dysprosiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16650, 'time' => $maxTime));
        $neodymiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16651, 'time' => $maxTime));
        $promethiumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16652, 'time' => $maxTime));
        $thuliumPrice = $db->fetchColumn('SELECT Price FROM Prices WHERE ItemId= :id AND Time= :time', array('id' => 16653, 'time' => $maxTime));
        //Get the item compositions
        $items = $db->fetchRowMany('SELECT Name,ItemId FROM ItemComposition');
        //Go through each of the items and update the price
        foreach($items as $item) {
            //Get the item composition
            $composition = $db->fetchRow('SELECT * FROM ItemComposition WHERE ItemId= :id', array('id' => $item['ItemId']));
            //Calculate the Batch Price
            $batchPrice = ( ($composition['Tritanium'] * $tritaniumPrice) +
                            ($composition['Pyerite'] * $pyeritePrice) +
                            ($composition['Mexallon'] * $mexallonPrice) +
                            ($composition['Isogen'] * $isogenPrice) +
                            ($composition['Nocxium'] * $nocxiumPrice) +
                            ($composition['Zydrine'] * $zydrinePrice) +
                            ($composition['Megacyte'] * $megacytePrice) + 
                            ($composition['Morphite'] * $morphitePrice) +
                            ($composition['HeavyWater'] * $heavyWaterPrice) +
                            ($composition['LiquidOzone'] * $liquidOzonePrice) +
                            ($composition['NitrogenIsotopes'] * $nitrogenIsotopesPrice) +
                            ($composition['HeliumIsotopes'] * $heliumIsotopesPrice) + 
                            ($composition['HydrogenIsotopes'] * $hydrogenIsotopesPrice) +
                            ($composition['OxygenIsotopes'] * $oxygenIsotopesPrice) +
                            ($composition['StrontiumClathrates'] * $strontiumClathratesPrice) +
                            ($composition['AtmosphericGases'] * $atmosphericGasesPrice) +
                            ($composition['EvaporiteDeposits'] * $evaporiteDepositsPirce) +
                            ($composition['Hydrocarbons'] * $hydrocarbonsPrice) +
                            ($composition['Silicates'] * $silicatesPrice) +
                            ($composition['Cobalt'] * $cobaltPrice) +
                            ($composition['Scandium'] * $scandiumPrice) +
                            ($composition['Titanium'] * $titaniumPrice) +
                            ($composition['Tungsten'] * $tungstenPrice) +
                            ($composition['Cadmium'] * $cadmiumPrice) +
                            ($composition['Platinum'] * $platinumPrice) +
                            ($composition['Vanadium'] * $vanadiumPrice) +
                            ($composition['Chromium'] * $chromiumPrice)+
                            ($composition['Technetium'] * $technetiumPrice) +
                            ($composition['Hafnium'] * $hafniumPrice) +
                            ($composition['Caesium'] * $caesiumPrice) +
                            ($composition['Mercury'] * $mercuryPrice) +
                            ($composition['Dysprosium'] * $dysprosiumPrice) +
                            ($composition['Neodymium'] * $neodymiumPrice) + 
                            ($composition['Promethium'] * $promethiumPrice) +
                            ($composition['Thulium'] * $thuliumPrice));
            //Calculate the batch price with the refine rate included
            //Batch Price is base price for everything
            $batchPrice = $batchPrice * $refineRate;
            //Calculate the unit price
            $price = $batchPrice / $composition['BatchSize'];
            //Calculate the m3 price
            $m3Price = $price / $composition['m3Size'];
            //Insert the prices into the Pricees table
            $db->insert('OrePrices', array(
                'Name' => $composition['Name'],
                'ItemId' => $composition['ItemId'],
                'BatchPrice' => $batchPrice,
                'UnitPrice' => $price,
                'm3Price' => $m3Price,
                'Time' => $time
            ));   
        }
        DBClose($db);
    }

    public function FetchNewPrices() {
        //Open a database connection
        $db = DBOpen();
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
            $url = 'https://market.fuzzwork.co.uk/aggregates/?region=10000002&types=' . $value;
            $item = FuzzworkPrice($url);
            $db->insert('Prices', array(
                'Name' => $key,
                'ItemId' => $value,
                'Price' => $item[$value]['sell']['median'],
                'Time' => $time
            ));
        }
        UpdateItemPricing();
        //Close the database connection
        DBClose($db);
    }
    
}