<?php

namespace App\Library;

use DB;

use App\Models\CorpJournal;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;


class SeatHelper {

    public function GetCorpJournal($corporationId) {
        //Setup the guzzle client
        $guzzle = new GuzzleHttp\Client([
            'headers' => [
                'X-Token' => 'tTa59bxP4VzBAfZ3s1JJ2BkEj8mFixD0',
            ],
        ]);
        $data = $guzzle->request('GET', 'https://seat.warpedintentions.com/api/v2/corporation/wallet-journal/{corporation_id}', [
            'corporation_id' => $corporationId,
        ]);
        dd($data);
    }
}

?>