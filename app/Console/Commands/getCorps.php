<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Commands\Library\CommandHelper;

use App\Models\Corporation\AllianceCorp;
use App\Models\ScheduledTask\ScheduleJob;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class GetCorpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:GetCorps';

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
        //Create the command helper container
        $task = new CommandHelper('CorpJournal');
        //Add the entry into the jobs table saying the job is starting
        $task->SetStartStatus();
        
        //Create the ESI container
        $esi = new Eseye();
        //try the  esi call to get all of the corporations in the alliance
        try {
            $corporations = $esi->invoke('get', '/alliances/{alliance_id}/corporations/', [
                'alliance_id' => 99004116,
            ]);
        } catch(\Seat\Eseye\Exceptions\RequestFailedException $e){
            dd($e->getEsiResponse());
        }
        //Delete all of the entries in the AllianceCorps table
        DB::table('AllianceCorps')->delete();
        foreach($corporations as $corp) {
            try {
                $corpInfo = $esi->invoke('get', '/corporations/{corporation_id}/', [
                    'corporation_id' => $corp,
                ]);
            } catch(\Seat\Eseye\Exceptions\RequestFailedException $e) {
                return $e->getEsiResponse();
            }
            $entry = new AllianceCorp;
            $entry->corporation_id = $corp;
            $entry->name = $corpInfo->name;
            $entry->save();
        }

        //Mark the job as finished
        $task->SetStopStatus();
    }
}
