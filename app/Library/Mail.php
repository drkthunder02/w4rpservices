<?php

namespace App\Library;

use DB;

use App\Models\EsiScope;
use App\Models\Esitoken;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class Mail {

    public function SendMail($charId, $taxAmount, $subject) {
        //Retrieve the token for Amund Risalo
        $token = DB::table('EsiTokens')->where('character_id', 93738489)->get();
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;
        $configuration->logfile_location = '/var/www/w4rpservices/storage/logs/eseye';
        //Create the ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        //Create the esi class variable
        $esi = new Eseye($authentication);
        try {
            $esi->setBody([
                'body' => $body,
                'receipients' => [
                    'recipient_id'=> $charId,
                    'recipient_type' => 'character',
                ],
                'subject' => $subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id' => 93738489,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
            return $e->getEsiResponse();
        }
    }
}

?>