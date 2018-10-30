<?php

/**
 * W4RP Services
 * GNU Public License
 */

 namespace App\Library;

 use Illuminate\Http\Request;
 use Session;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use App\User;

class Finances {

    protected $refreshToken;
    protected $expires;

    private $esi;

    public function __construct() {
        $user = DB::table('users')->where('name', 'Minerva Arbosa')->first();
        
        $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
            'client_id' => env('ESI_CLIENT_ID'),
            'secret' => env('ESI_SECRET_KEY'),
            'refresh_token' => $user->refresh_token,
        ]);

        $this->esi = new \Seat\Eseye\Eseye($authentication);

        
    }

    public function GetMarketGroups() {
        $instance = new \Seat\Eseye\Eseye();

        $marketGroups = $instance->invoke('get', '/markets/groups/');
    }

    public function GetMasterWalletJournal() {
        $journal = $this->esi->invoke('get', '/corporations/98287666/wallets/1/journal/');
        $journal = json_decode($journal->raw, true);

        return $journal;
    }
}

?>