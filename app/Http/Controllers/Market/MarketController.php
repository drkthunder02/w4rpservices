<?php

namespace App\Http\Controllers\Market;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Taxes\TaxesHelper;

//Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Finances\CorpMarketJournal;
use App\Models\Finances\CorpMarketStructure;

class MarketController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:structure.market');
    }

    public function displayAddMarketTax() {
        return view('market.add.display');
    }

    public function storeAddMarketTax(Request $request) {
        $this->validate($request, [
            'tax' => 'required',
        ]);

        $charId = auth()->user()->getId();

        //Declare the esi helper
        $esiHelper = new Esi;
        //Create the esi container to get the character's public information
        $esi = new Eseye();
        try {
            $charInfo = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestExceptionFailed $e) {
            return redirect('/market/add')->with('erorr', 'Failed to get character info.');
        }   

        $ratio = $request->tax / 2.5;

        $corpMarket = new CorpMarketStructure;
        $corpMarket->character_id = $charId;
        $corpMarket->corporation_id = $charInfo->corporation_id;
        $corpMarket->tax = $request->tax;
        $corpMarket->ratio = $ratio;
        $corpMarket->save();

        return redirect('/dahsboard')->with('success', 'Market structure recorded.');
    }

    public function displayTaxes() {
        $charId = auth()->user()->getId();
        
        $esi = new Eseye();
        try {
            $charInfo = $esi->invoke('get', '/characters/{character_id}/', [
                'character_id' => $charId,
            ]);
        } catch(RequestExceptionFailed $e) {
            return redirect('/market/add')->with('erorr', 'Failed to get character info.');
        }

        //Get the total taxes from the database

    }

}
