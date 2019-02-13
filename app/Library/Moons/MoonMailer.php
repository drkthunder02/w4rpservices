<?php

namespace App\Library\Moons;

use App\Models\Mail\EveMail;

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

        SendEveMail::dispatch($mail);
    }
}

?>