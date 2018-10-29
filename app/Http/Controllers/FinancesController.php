<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Socialite;
use Auth;
use App\User;
use App\Libary\Finances;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class FinancesController extends Controller
{
    public function redirectToProvider() {
        if(!Auth::check()) {
            return redirect()->to('/');
        }

        return Socialite::driver('eveonline')->setScopes(['publicData', 'esi-wallet.read_corporation_wallets.v1'])->redirect();
    }

    public function displayWallet() {
        $esi = new \App\Library\Finances();

        //Get the Journal Entries and just return them
        $journals = $esi->GetMasterWalletJournal();
        $journals = json_decode($journals->raw, true);
        dd($journals);
        return $journals;
    }
}
