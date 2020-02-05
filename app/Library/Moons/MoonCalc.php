<?php
/* 
 *  W4RP Services
 *  GNU Public License
 */

namespace App\Library\Moons;

//Internal Library
use Session;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

//Models
use App\Models\Moon\Config;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\Moon;
use App\Models\Moon\OrePrice;
use App\Models\Moon\Price;

class MoonCalc {

    public function GetOreComposition($ore) {
        $composition = ItemComposition::where([
            'Name' => $ore,
        ])->first();

        return $composition;
    }

    public function SpatialMoonsTotalWorth($firstOre, $firstQuan, $secondOre, $secondQuan, $thirdOre, $thirdQuan, $fourthOre, $fourthQuan) {
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
       
        //Return the rental price to the caller
        return $totalPriceMined;
    }

    public function SpatialMoonsOnlyGooTotalWorth($firstOre, $firstQuan, $secondOre, $secondQuan, $thirdOre, $thirdQuan, $fourthOre, $fourthQuan) {
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
            if($this->IsRMoonGoo($firstOre)) {
                $firstTotal = $this->CalcPrice($firstOre, $firstPerc);
            } else {
                $firstTotal = 0.00;
            }
        } else {
            $firstTotal = 0.00;
        }
        if($secondOre != "None") {
            if($this->IsRMoonGoo($secondOre)) {
                $secondTotal = $this->CalcPrice($secondOre, $secondPerc);
            } else {
                $secondTotal = 0.00;
            }
        } else {
            $secondTotal = 0.00;
        }
        if($thirdOre != "None") {
            if($this->IsRMoonGoo($thirdOre)) {
                $thirdTotal = $this->CalcPrice($thirdOre, $thirdPerc);
            } else {
                $thirdTotal = 0.00;
            }
        } else {
            $thirdTotal = 0.00;
        }
        if($fourthOre != "None") {
            if($this->IsRMoonGoo($fourthOre)) {
                $fourthTotal = $this->CalcPrice($fourthOre, $fourthPerc);
            } else {
                $fourthTotal = 0.00;
            }
        } else {
            $fourthTotal = 0.00;
        }
        //Calculate the total to price to be mined in one month
        $totalPriceMined = $firstTotal + $secondTotal + $thirdTotal + $fourthTotal;
       
        //Return the rental price to the caller
        return $totalPriceMined;
    }

    public function SpatialMoonsOnlyGooMailer($firstOre, $firstQuan, $secondOre, $secondQuan, $thirdOre, $thirdQuan, $fourthOre, $fourthQuan) {
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
            if($this->IsRMoon($firstOre)) {
                $firstTotal = $this->CalcPrice($firstOre, $firstPerc);
            } else {
                $firstTotal = 0.00;
            }
        } else {
            $firstTotal = 0.00;
        }
        if($secondOre != "None") {
            if($this->IsRMoon($secondOre)) {
                $secondTotal = $this->CalcPrice($secondOre, $secondPerc);
            } else {
                $secondTotal = 0.00;
            }
        } else {
            $secondTotal = 0.00;
        }
        if($thirdOre != "None") {
            if($this->IsRMoon($thirdOre)) {
                $thirdTotal = $this->CalcPrice($thirdOre, $thirdPerc);
            } else {
                $thirdTotal = 0.00;
            }
        } else {
            $thirdTotal = 0.00;
        }
        if($fourthOre != "None") {
            if($this->IsRMoon($fourthOre)) {
                $fourthTotal = $this->CalcPrice($fourthOre, $fourthPerc);
            } else {
                $fourthTotal = 0.00;
            }
        } else {
            $fourthTotal = 0.00;
        }
        //Calculate the total to price to be mined in one month
        $totalPriceMined = $firstTotal + $secondTotal + $thirdTotal + $fourthTotal;
        //Calculate the rental price.  Refined rate is already included in the price from rental composition
        $rentalPrice['alliance'] = $totalPriceMined * ($config[0]->RentalTax / 100.00);
        $rentalPrice['outofalliance'] = $totalPriceMined * ($config[0]->AllyRentalTax / 100.00);
       
        //Return the rental price to the caller
        return $rentalPrice;
    }

    public function SpatialMoonsOnlyGoo($firstOre, $firstQuan, $secondOre, $secondQuan, $thirdOre, $thirdQuan, $fourthOre, $fourthQuan) {
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
            if($this->IsRMoon($firstOre)) {
                $firstTotal = $this->CalcPrice($firstOre, $firstPerc);
            } else {
                $firstTotal = 0.00;
            }
        } else {
            $firstTotal = 0.00;
        }
        if($secondOre != "None") {
            if($this->IsRMoon($secondOre)) {
                $secondTotal = $this->CalcPrice($secondOre, $secondPerc);
            } else {
                $secondTotal = 0.00;
            }
        } else {
            $secondTotal = 0.00;
        }
        if($thirdOre != "None") {
            if($this->IsRMoon($thirdOre)) {
                $thirdTotal = $this->CalcPrice($thirdOre, $thirdPerc);
            } else {
                $thirdTotal = 0.00;
            }
        } else {
            $thirdTotal = 0.00;
        }
        if($fourthOre != "None") {
            if($this->IsRMoon($fourthOre)) {
                $fourthTotal = $this->CalcPrice($fourthOre, $fourthPerc);
            } else {
                $fourthTotal = 0.00;
            }
        } else {
            $fourthTotal = 0.00;
        }
        //Calculate the total to price to be mined in one month
        $totalPriceMined = $firstTotal + $secondTotal + $thirdTotal + $fourthTotal;
        //Calculate the rental price.  Refined rate is already included in the price from rental composition
        $rentalPrice['alliance'] = $totalPriceMined * ($config[0]->RentalTax / 100.00);
        $rentalPrice['outofalliance'] = $totalPriceMined * ($config[0]->AllyRentalTax / 100.00);
        //Format the rental price to the appropriate number
        $rentalPrice['alliance'] = number_format($rentalPrice['alliance'], 0, ".", ",");
        $rentalPrice['outofalliance'] = number_format($rentalPrice['outofalliance'], 0, ".", ",");
       
        //Return the rental price to the caller
        return $rentalPrice;
    }

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
        $rentalPrice['alliance'] = $totalPriceMined * ($config[0]->RentalTax / 100.00);
        $rentalPrice['outofalliance'] = $totalPriceMined * ($config[0]->AllyRentalTax / 100.00);
        //Format the rental price to the appropriate number
        $rentalPrice['alliance'] = number_format($rentalPrice['alliance'], 0, ".", ",");
        $rentalPrice['outofalliance'] = number_format($rentalPrice['outofalliance'], 0, ".", ",");
       
        //Return the rental price to the caller
        return $rentalPrice;
    }

    public function FetchNewPrices() {
        //Create the item id array which we will pull data for from Fuzzwork market api
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

        //Create the time variable
        $time = Carbon\Carbon::now();

        //Get the json data for each ItemId from https://market.fuzzwork.co.uk/api/
        //Base url is https://market.fuzzwork.co.uk/aggregates/?region=10000002&types=34
        //Going to use curl for these requests
        foreach($ItemIDs as $key => $value) {
            //Declare a new array each time we cycle through the for loop for the item
            $item = array();

            //Setup the guzzle client fetch object
            $client = new Client(['base_uri' => 'https://market.fuzzwork.co.uk/aggregates/']);
            //Setup the uri for the guzzle client
            $uri = '?region=10000002&types=' . $value;
            //Get the result from the guzzle client request
            $result = $client->request('GET', $uri);
            //Decode the request into an array from the json body return
            $item = json_decode($result->getBody(), true);

            //Save the entry into the database
            $price = new Price;
            $price->Name = $key;
            $price->ItemId = $value;
            $price->Price = $item[$value]['sell']['median'];
            $price->Time = $time;
            $price->save();
        }
        
        //Run the update for the item pricing
        $this->UpdateItemPricing();
    }

    private function UpdateItemPricing() {
        //Get the configuration from the config table
        $config = DB::table('Config')->first();

        //Calculate refine rate
        $refineRate = $config->RefineRate / 100.00;
        
        //Calculate the current time
        $time = Carbon\Carbon::now();
        //Calcualate the current time minus 30 days
        $pastTime = Carbon::now()->subDays(30);

        //Get the price of the basic minerals
        $tritaniumPrice = MineralPrice::where(['ItemId' => 34])->whereDate('Time', '>', $pastTime)->avg('Price');
        $pyeritePrice = MineralPrice::where(['ItemId' => 35])->whereDate('Time', '>', $pastTime)->avg('Price');
        $mexallonPrice = MineralPrice::where(['ItemId' => 36])->whereDate('Time', '>', $pastTime)->avg('Price');
        $isogenPrice = MineralPrice::where(['ItemId' => 37])->whereDate('Time', '>', $pastTime)->avg('Price');
        $nocxiumPrice = MineralPrice::where(['ItemId' => 38])->whereDate('Time', '>', $pastTime)->avg('Price');
        $zydrinePrice = MineralPrice::where(['ItemId' => 39])->whereDate('Time', '>', $pastTime)->avg('Price');
        $megacytePrice = MineralPrice::where(['ItemId' => 40])->whereDate('Time', '>', $pastTime)->avg('Price');
        $morphitePrice = MineralPrice::where(['ItemId' => 11399])->whereDate('Time', '>', $pastTime)->avg('Price');
        $heliumIsotopesPrice = MineralPrice::where(['ItemId' => 16274])->whereDate('Time', '>', $pastTime)->avg('Price');
        $nitrogenIsotopesPrice = MineralPrice::where(['ItemId' => 17888])->whereDate('Time', '>', $pastTime)->avg('Price');
        $oxygenIsotopesPrice = MineralPrice::where(['ItemId' => 17887])->whereDate('Time', '>', $pastTime)->avg('Price');
        $hydrogenIsotopesPrice = MineralPrice::where(['ItemId' => 17889])->whereDate('Time', '>', $pastTime)->avg('Price');
        $liquidOzonePrice = MineralPrice::where(['ItemId' => 16273])->whereDate('Time', '>', $pastTime)->avg('Price');
        $heavyWaterPrice = MineralPrice::where(['ItemId' => 16272])->whereDate('Time', '>', $pastTime)->avg('Price');
        $strontiumClathratesPrice = MineralPrice::where(['ItemId' => 16275])->whereDate('Time', '>', $pastTime)->avg('Price');
        //Get the price of the moongoo
        $atmosphericGasesPrice = MineralPrice::where(['ItemId' => 16634])->whereDate('Time', '>', $pastTime)->avg('Price');
        $evaporiteDepositsPirce = MineralPrice::where(['ItemId' => 16635])->whereDate('Time', '>', $pastTime)->avg('Price');
        $hydrocarbonsPrice = MineralPrice::where(['ItemId' => 16633])->whereDate('Time', '>', $pastTime)->avg('Price');
        $silicatesPrice = MineralPrice::where(['ItemId' => 16636])->whereDate('Time', '>', $pastTime)->avg('Price');
        $cobaltPrice = MineralPrice::where(['ItemId' => 16640])->whereDate('Time', '>', $pastTime)->avg('Price');
        $scandiumPrice = MineralPrice::where(['ItemId' => 16639])->whereDate('Time', '>', $pastTime)->avg('Price');
        $titaniumPrice = MineralPrice::where(['ItemId' => 16638])->whereDate('Time', '>', $pastTime)->avg('Price');
        $tungstenPrice = MineralPrice::where(['ItemId' => 16637])->whereDate('Time', '>', $pastTime)->avg('Price');
        $cadmiumPrice = MineralPrice::where(['ItemId' => 16643])->whereDate('Time', '>', $pastTime)->avg('Price');
        $platinumPrice = MineralPrice::where(['ItemId' => 16644])->whereDate('Time', '>', $pastTime)->avg('Price');
        $vanadiumPrice = MineralPrice::where(['ItemId' => 16642])->whereDate('Time', '>', $pastTime)->avg('Price');
        $chromiumPrice = MineralPrice::where(['ItemId' => 16641])->whereDate('Time', '>', $pastTime)->avg('Price');
        $technetiumPrice = MineralPrice::where(['ItemId' => 16649])->whereDate('Time', '>', $pastTime)->avg('Price');
        $hafniumPrice = MineralPrice::where(['ItemId' => 16648])->whereDate('Time', '>', $pastTime)->avg('Price');
        $caesiumPrice = MineralPrice::where(['ItemId' => 16647])->whereDate('Time', '>', $pastTime)->avg('Price');
        $mercuryPrice = MineralPrice::where(['ItemId' => 16646])->whereDate('Time', '>', $pastTime)->avg('Price');
        $dysprosiumPrice = MineralPrice::where(['ItemId' => 16650])->whereDate('Time', '>', $pastTime)->avg('Price');
        $neodymiumPrice = MineralPrice::where(['ItemId' => 16651])->whereDate('Time', '>', $pastTime)->avg('Price');
        $promethiumPrice = MineralPrice::where(['ItemId' => 16652])->whereDate('Time', '>', $pastTime)->avg('Price');
        $thuliumPrice = MineralPrice::where(['ItemId' => 16653])->whereDate('Time', '>', $pastTime)->avg('Price');
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
            //Update the prices in the Prices table
            DB::table('OrePrices')->where('Name', $composition[0]->Name)->update([
                'Name' => $composition[0]->Name,
                'ItemId' => $composition[0]->ItemId,
                'BatchPrice' => $batchPrice,
                'UnitPrice' => $price,
                'm3Price' => $m3Price,
            ]);
        }
    }

    public function CalcOreUnits($ore, $percentage) {
        //Specify the total pull amount
        $totalPull = 5.55 * (3600.00 * 24.00 *30.00);
        //Find the size of the asteroid from the database
        $item = ItemComposition::where([
            'Name' => $ore,
        ])->first();
        //Get the m3 size from the item composition
        $m3Size = $item->m3Size;
        //Calculate the actual m3 from the total pull amount in m3 using the percentage of the ingredient
        $actualm3 = floor($totalPull * $percentage);
        //Calculate the units from the m3 pulled from the moon
        $units = floor($actualm3 / $m3Size);   

        return $units;
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
        $unitPrice = DB::table('OrePrices')->where('Name', $ore)->value('UnitPrice');
        //Calculate the total amount from the units and unit price
        $total = $units * $unitPrice;
        //Return the value
        return $total;
    }

    private function ConvertToPercentage($quantity) {
        return $quantity / 100.00;
    }

    private function IsRMoonGoo($ore) {
        $ores = [
            'Zeolites' => 'Gas',
            'Sylvite' => 'Gas',
            'Bitumens' => 'Gas',
            'Coesite' => 'Gas',
            'Cobaltite' => 'R8',
            'Euxenite' => 'R8',
            'Titanite' => 'R8',
            'Scheelite' => 'R8',
            'Otavite' => 'R16',
            'Sperrylite' => 'R16',
            'Vanadinite' => 'R16',
            'Chromite' => 'R16',
            'Carnotite' => 'R32',
            'Zircon' => 'R32',
            'Pollucite' => 'R32',
            'Cinnabar' => 'R32',
            'Xenotime' => 'R64',
            'Monazite' => 'R64',
            'Loparite' => 'R64',
            'Ytterbite' => 'R64',
        ];

        foreach($ores as $key => $value) {
            
            if(strtolower($key) == strtolower($ore)) {
                return true;
            }
        }

        return false;
    }

    private function IsRMoon($ore) {
        $ores = [
            'Prime Arkonor' => 'Null',
            'Cubic Bistot' => 'Null',
            'Pellucid Crokite' => 'Null',
            'Jet Ochre' => 'Null',
            'Zeolites' => 'Gas',
            'Sylvite' => 'Gas',
            'Bitumens' => 'Gas',
            'Coesite' => 'Gas',
            'Cobaltite' => 'R8',
            'Euxenite' => 'R8',
            'Titanite' => 'R8',
            'Scheelite' => 'R8',
            'Otavite' => 'R16',
            'Sperrylite' => 'R16',
            'Vanadinite' => 'R16',
            'Chromite' => 'R16',
            'Carnotite' => 'R32',
            'Zircon' => 'R32',
            'Pollucite' => 'R32',
            'Cinnabar' => 'R32',
            'Xenotime' => 'R64',
            'Monazite' => 'R64',
            'Loparite' => 'R64',
            'Ytterbite' => 'R64',
        ];

        foreach($ores as $key => $value) {
            
            if(strtolower($key) == strtolower($ore)) {
                return true;
            }
        }

        return false;
    }
}