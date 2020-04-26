<?php

use Illuminate\Database\Seeder;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\Moon\Config;
use App\Models\Moon\RentalMoon;
use App\Models\Moon\ItemComposition;
use App\Models\Moon\OrePrice;
use App\Models\Moon\MineralPrice;

class OrePricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->FetchNewPrices();
        $this->UpdateItemPricing();
    }

    private function UpdateItemPricing() {
        //Get the configuration from the config table
        $config = DB::table('Config')->first();
        //Calculate refine rate
        $refineRate = $config->RefineRate / 100.00;
        //Calculate the current time
        $time = Carbon::now();
        
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
            $composition = ItemComposition::where('ItemId', $item->ItemId)->first();
    
            //Calculate the Batch Price
            $batchPrice = ( ($composition->Tritanium * $tritaniumPrice) +
                            ($composition->Pyerite * $pyeritePrice) +
                            ($composition->Mexallon * $mexallonPrice) +
                            ($composition->Isogen * $isogenPrice) +
                            ($composition->Nocxium * $nocxiumPrice) +
                            ($composition->Zydrine * $zydrinePrice) +
                            ($composition->Megacyte * $megacytePrice) + 
                            ($composition->Morphite * $morphitePrice) +
                            ($composition->HeavyWater * $heavyWaterPrice) +
                            ($composition->LiquidOzone * $liquidOzonePrice) +
                            ($composition->NitrogenIsotopes * $nitrogenIsotopesPrice) +
                            ($composition->HeliumIsotopes * $heliumIsotopesPrice) + 
                            ($composition->HydrogenIsotopes * $hydrogenIsotopesPrice) +
                            ($composition->OxygenIsotopes * $oxygenIsotopesPrice) +
                            ($composition->StrontiumClathrates * $strontiumClathratesPrice) +
                            ($composition->AtmosphericGases * $atmosphericGasesPrice) +
                            ($composition->EvaporiteDeposits * $evaporiteDepositsPirce) +
                            ($composition->Hydrocarbons * $hydrocarbonsPrice) +
                            ($composition->Silicates * $silicatesPrice) +
                            ($composition->Cobalt * $cobaltPrice) +
                            ($composition->Scandium * $scandiumPrice) +
                            ($composition->Titanium * $titaniumPrice) +
                            ($composition->Tungsten * $tungstenPrice) +
                            ($composition->Cadmium * $cadmiumPrice) +
                            ($composition->Platinum * $platinumPrice) +
                            ($composition->Vanadium * $vanadiumPrice) +
                            ($composition->Chromium * $chromiumPrice)+
                            ($composition->Technetium * $technetiumPrice) +
                            ($composition->Hafnium * $hafniumPrice) +
                            ($composition->Caesium * $caesiumPrice) +
                            ($composition->Mercury * $mercuryPrice) +
                            ($composition->Dysprosium * $dysprosiumPrice) +
                            ($composition->Neodymium * $neodymiumPrice) + 
                            ($composition->Promethium * $promethiumPrice) +
                            ($composition->Thulium * $thuliumPrice));
            
                            //Calculate the batch price with the refine rate included
            //Batch Price is base price for everything
            $batchPrice = $batchPrice * $refineRate;
            
            //Calculate the unit price
            $price = $batchPrice / $composition->BatchSize;
            
            //Calculate the m3 price
            $m3Price = $price / $composition->m3Size;  
            
            //Insert the prices into the Prices table
            $ore = new OrePrice;
            $ore->Name = $composition->Name;
            $ore->ItemId = $composition->ItemId;
            $ore->BatchPrice = $batchPrice;
            $ore->UnitPrice = $price;
            $ore->m3Price = $m3Price;
            $ore->Time = $time;
            $ore->save();
        }
    }

    private function FetchNewPrices() {
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
                'Time' => $time
            ]);
        }
    }
}
