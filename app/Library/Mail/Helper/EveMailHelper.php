<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Mail\Helper;

//Internal Library
use Log;

//Job
use App\Jobs\ProcessSendEveMailJob;

//Models
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;
use App\Models\Jobs\JobSendEveMail;

//Library
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

//Seat Stuff
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;

class EveMailHelper {
    
    public function SendEveMail($sender, $subject, $body, $recipient, $rType) {
        //Get the esi config from the environment file
        $config = config('esi');

        //Declare the ESI Helper
        $esiHelper = new Esi;

        //Check for the correct scope
        if(!$esiHelper->HaveEsiScope($sender, 'esi-mail.send_mail.v1')) {
            Log::critical('Could not find correct scope for the token for the mailer.');
            return null;
        }

        //Retrieve token from from the database for the sender
        $token = $esiHelper->GetRefreshToken($sender);

        //Create the ESI authentication container
        $esi = $esiHelper->SetupEsiAuthentication($token);

        //Set caching to null
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        //Attempt to send the mail
        try {
            $esi->setBody([
                'approved_cost' => 100,
                'body' => $this->body,
                'recipients' => [[
                    'recipient_id' => $this->recipient,
                    'recipient_type' => $this->recipient_type,
                ]],
                'subject' => $this->subject,
            ])->invoke('post', '/characters/{character_id}/mail/', [
                'character_id'=> $this->sender,
            ]);
        } catch(RequestFailedException $e) {
            Log::warning($e);
            return null;
        }
    }
}