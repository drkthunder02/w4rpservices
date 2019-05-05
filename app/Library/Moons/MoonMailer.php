<?php

namespace App\Library\Moons;

//Jobs
use App\Jobs\SendEveMailJob;

//Models
use App\Models\Jobs\JobSendEveMail;

class MoonMailer {

    public function GetRentalMoons() {
        
    }

    public function TotalizeMoonCost($moons) {

    }
    
    public function SendMail($recipient, $moons, $dueDate) {
        
        $body = '';
        
        $mail = new EveMail;
        $mail->sender = 93738489;
        $mail->subject = 'Moon Rental';
        $mail->body = $body;
        $mail->recipient = (int)$recipient;
        $mail->recipient_type = 'character';
        $mail->save();

        SendEveMailJob::dispatch($mail);
    }
}

?>