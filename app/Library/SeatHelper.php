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
        $guzzle = new Client([
            'headers' => [
                'X-Token' => 'EXXruHji5xYGO07C9W31cDjjZ0D3nPVw',
            ],
        ]);
        $data = $guzzle->request('GET', 'https://seat.warpedintentions.com/api/v2/corporation/wallet-journal/' . $corporationId);
        $body = $data->getBody();
        dd($body);
    }

    private function DecodeDate($date) {
        //Find the end of the date
        $dateEnd = strpos($date, "T");
        //Split the string up into date and time
        $dateArr = str_split($date, $dateEnd);
        //Trim the T and Z from the end of the second item in the array
        $dateArr[1] = ltrim($dateArr[1], "T");
        $dateArr[1] = rtrim($dateArr[1], "Z");
        //Combine the date
        $realDate = $dateArr[0] . " " . $dateArr[1];

        //Return the combined date in the correct format
        return $realDate;
    }
}

?>