<?php

namespace App\Library\Moons;

//Internal Library
use Carbon\Carbon;

//Jobs
use App\Jobs\SendEveMailJob;

//Library
use App\Library\Moons\MoonCalc;

//Models
use App\Models\Jobs\JobSendEveMail;
use App\Models\Mail\SentMail;
use App\Models\Moon\Moon;
use App\Models\MoonRent\MoonRent;

class MoonMailer {
    public function DeleteMoonRent(MoonRent $rental, Carbon $today) {
        if($today->greaterThanOrEqualTo($rental->RentalEnd)) {
            MoonRent::where(['id' => $rental->id])->delete();
        }
    }

    public function PaidUntil(MoonRent $rental) {
        return $rental->paid_until;
    }

    public function UpdateNotPaid(MoonRent $rental) {
        $today = Carbon::now();

        if($today >= $rental->Paid_Until) {
            MoonRent::where([
                'System' => $rental->System,
                'Planet'=> $rental->Planet,
                'Moon'=> $rental->Moon,
            ])->update([
                'Paid' => 'No',
            ]);
        }
    }

    public function SaveSentRecord($sender, $subject, $body, $recipient, $recipientType) {
        $sentmail = new SentMail;
        $sentmail->sender = $sender;
        $sentmail->subject = $subject;
        $sentmail->body = $body;
        $sentmail->recipient = $recipient;
        $sentmail->recipient_type = $recipientType;
        $sentmail->save();
    }

    public function GetMoonList(MoonRent $moons) {
        //Declare the variable to be used as a global part of the function
        $list = array();

        //For each of the moons, build the System Planet and Moon.
        foreach($moons as $moon) {
            $temp = 'System: ' . $moon->System;
            $temp .= 'Planet: ' . $moon->Planet;
            $temp .= 'Moon: ' . $moon->Moon;
            //Push the new string onto the array list
            array_push($list, $temp);
        }

        //Return the list
        return $list;
    }

    public function GetRentalMoons($contact) {
        $rentals = MoonRent::where([
            'Contact' => $contact,
        ])->get();

        return $rentals;
    }

    public function TotalizeMoonCost($rentals) {
        //Delcare variables and classes
        $moonCalc = new MoonCalc;
        $totalCost = 0.00;

        foreach($rentals as $rental) {
            $moon = Moon::where([
                'System' => $rental->System,
                'Planet' => $rental->Planet,
                'Moon' => $rental->Moon,
            ])->first();

            //Get the updated price for the moon
            $price = $moonCalc->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                    $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

            //Check the type and figure out which price to add in
            if($rental->Type == 'alliance') {
                $totalCost += $price['alliance'];
            } else{
                $totalCost += $price['outofalliance'];
            }
        }

        //Return the total cost back to the calling function
        return $totalCost;
    }

    public function GetRentalType($rentals) {
        $alliance = 0;
        $outofalliance = 0;
  
        //Go through the data and log whether the renter is in the alliance,
        //or the renter is out of the alliance
        foreach($rentals as $rental) {
            if($rental->Type == 'alliance') {
                $alliance++;
            } else {
                $outofalliance++;
            }
        }

        //Return the rental type
        if($alliance > $outofalliance) {
            return 'alliance';
        } else {
            return 'outofalliance';
        }
    }
}
