<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Commands\Library\CommandHelper;
use App\Library\Moons\MoonMailer;
use App\Library\Moons\MoonCalc;
use App\Library\Lookups\LookupHelper;
use DB;
use Carbon\Carbon;

use App\Models\Moon\Moon;

class MoonMailerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:MoonMailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mail out the moon rental bills automatically';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Create the new command helper container
        $task = new CommandHelper('MoonMailer');
        //Add the entry into the jobs table saying the job has started
        $task->SetStartStatus();

        //Create class helpers
        $lookup = new LookupHelper();
        $moonTotal = new MoonCalc();

        //Get all of the moon rental contacts
        $contacts = DB::table('Moons')->pluck('Contact');

        //Cycle through each contact, and get all of the moons for the contact, then
        //totalize the cost
        $total = 0.00;
        $entityType = '';
        foreach($contacts as $contact) {
            if($contact != 0) {
                //Get whether the contact is part of the alliance or an ally.
                $corpId = $lookup->LookupCharacter($contact);
                $allianceId = $lookup->LookupCorporation($corpId);
                if($allianceId == 99004116) {
                    $entityType = 'W4RP';
                } else {
                    $entityType = 'Other';
                }
                
                //Get all of the moons a particular contact is renting.
                $moons = Moon::where(['Contact' => $contact])->get();

                //Price the moons based on whether the contact is of the alliance or an ally
                foreach($moons as $moon) {
                    $moon = $moontTotal->SpatialMoonsOnlyGoo($moon->FirstOre, $moon->FirstQuantity, $moon->SecondOre, $moon->SecondQuantity, 
                                                             $moon->ThirdOre, $moon->ThirdQuantity, $moon->FourthOre, $moon->FourthQuantity);

                    if($entityType = 'W4RP') {
                        $total += $moon['alliance'];
                    } else {
                        $total += $moon['outofalliance'];
                    }
                }
            }
        }

        //Send a mail to the contact listing the moons, and how much is owed

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
