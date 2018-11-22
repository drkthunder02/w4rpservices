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
                'X-Token' => 'tTa59bxP4VzBAfZ3s1JJ2BkEj8mFixD0',
            ],
        ]);
        $data = $guzzle->request('GET', 'https://seat.warpedintentions.com/api/v2/corporation/wallet-journal/{corporation_id}', [
            'corporation_id' => $corporationId,
        ]);
        dd($data);
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