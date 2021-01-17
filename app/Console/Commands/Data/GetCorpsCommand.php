<?php

namespace App\Console\Commands\Data;

//Internal Library
use Illuminate\Console\Command;

//Models
use App\Models\Corporation\AllianceCorp;
use App\Models\ScheduledTask\ScheduleJob;

//Library
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

class GetCorpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:GetCorps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get corporations in alliance and store in db.';

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
        //Declare some variables
        $esiHelper = new Esi;
        
        $esi = $esiHelper->SetupEsiAuthentication();

        //try the  esi call to get all of the corporations in the alliance
        try {
            $corporations = $esi->invoke('get', '/alliances/{alliance_id}/corporations/', [
                'alliance_id' => 99004116,
            ]);
        } catch(RequestFailedException $e){
            dd($e->getEsiResponse());
        }
        //Delete all of the entries in the AllianceCorps table
        AllianceCorp::truncate();

        //Foreach corporation, make entries into the database.
        foreach($corporations as $corp) {
            try {
                $corpInfo = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corp,
                ]);
            } catch(RequestFailedException $e) {
                return $e->getEsiResponse();
            }
            $entry = new AllianceCorp;
            $entry->corporation_id = $corp;
            $entry->name = $corpInfo->name;
            $entry->save();
        }
    }
}
