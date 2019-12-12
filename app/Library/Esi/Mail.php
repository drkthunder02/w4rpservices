<?php

namespace App\Library\Esi;

//Models
use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

//Library
use App\Library\Esi\Esi;
use Seat\Eseye\Exceptions\RequestFailedException;

class Mail {

    public function SendMail($recipient, $rType, $subject, $body) {
        //Declare some variables
        $esiHelper = new Esi;

        //Get the esi config
        $config = config('esi');

        //Retrieve the token for main character to send mails from
        $token = $esiHelper->GetRefreshToken($config['primary']);
        //Create the ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Try to send the mail
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
                'character_id'=> $config['primary'],
            ]);
        } catch(RequestFailedException $e) {
            return 1;
        }

        return 0;
    }
}

?>