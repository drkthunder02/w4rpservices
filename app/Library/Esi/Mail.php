<?php

namespace App\Library\Esi;

use DB;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

class Mail {

    public function SendMail($recipient, $rType, $subject, $body) {
        //Retrieve the token for main character to send mails from
        $token = EsiToken::where(['character_id' => 93738489])->first();
        //Create the ESI authentication container
        $config = config('esi');
        $authentication = new EsiAuthentication([
            'client_id'  => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token->refresh_token,
        ]);
        $esi = new Eseye($authentication);
        try {
            $esi->setBody([
                'approved_cost' => 0,
                'body' => $body,
                'recipients' => [[
                    'recipient_id' => (int)$recipient,
                    'recipient_type' => $rType,
                ]],
                'subject' => $subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id'=> 93738489,
            ]);
        } catch(RequestFailedException $e) {
            return 1;
        }

        return 0;
    }
}

?>