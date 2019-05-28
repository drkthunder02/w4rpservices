<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

//App Library
use App\Library\Structures\StructureHelper;
use App\Jobs\Library\JobHelper;

//App Models
use App\Models\Jobs\JobProcessStructure;
use App\Models\Job\JobStatus;
use App\Models\Structure\Structure;

class ProcessStructureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 300;

    /**
     * Number of job retries
     */
    public $tries = 3;

    /**
     * Job Variables
     */
    private $charId;
    private $corpId;
    private $structure;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessStructure $jps)
    {
        $this->charId = $jps->charId;
        $this->corpId = $jps->corpId;
        $this->structure = $jps->structure;

        //Set the connection for the job
        $this->connection = 'redis';
    }

    /**
     * Execute the job.
     * The job's task is to get all of the information for a particular structure
     * and store it in the database.  This task can take a few seconds because of the ESI
     * calls required to store the information.  We leave this type of job up to the queue
     * in order to take the load off of the cron job.
     *
     * @return void
     */
    public function handle()
    {
        //Declare the structure helper class.
        $structureHelper = new StructureHelper();

        //Using our private function make sure we have the necessrary ESI scope
        if($this->HasEsiScope() == false) {
            Log::critical("Job couldn't be completed because of ESI Scopes.");
            return null;
        }

        //Setup the Eseye container and authenticate it.
        $config = config('esi');
        $token = EsiToken::where(['character_id' => 93738489])->get(['refresh_token']);
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);

        //Declare the esi variable
        $esi = new Eseye($authentication);

        try {
            $info = $esi->invoke('get', '/universe/structures/{structure_id}/', [
                'structure_id' => $this->structure['structure_id'],
            ]);
        } catch(RequestFailedException $e) {
            return null;
        }

        //Attempt to get the solar system name from ESI
        try {
            $solarName = $esi->invoke('get', '/universe/systems/{system_id}/', [
                'system_id' => $info['solary_system_id'],
            ]);
        } catch(RequestFailedException $e) {
            $solarName = null;
        }

        //Record the structure information into the database
        //Find if the structure exists
        $found = Structure::where(['structure_id' => $this->structure['structure_id']])->get();
        if($found) {

        } else {
            $structure = new Structure;
            $structure->structure_id = $this->structure['structure_id'];
            $structure->structure_name = $info['name'];
            $structure->corporation_id = $info['owner_id'];
            $structure->solar_system_id = $info['solar_system_id'];
            $structure->solary_system_name = $solarName;
            if(isset($info['type_id'])) {
                $structure->type_id = $info['type_id'];
            }
            $structure->corporation_id = $this->structure['corporation_id'];
            if(isset($this->structures['services'])) {
                $structure->services = true;
            } else {
                $structure->services = false;
            }
            if(isset($this->structure['state_timer_start'])) {
                $structure->state_timer_start = $this->DecodeDate($this->structure['state_timer_start']);
            }
            if(isset($this->structure['state_timer_end'])) {
                $structure->state_timer_end = $this->DecodeDate($this->structure['state_timer_end']);
            }
            if(isset($this->structure['fuel_expires'])) {
                $structure->fuel_expires = $this->structure['fuel_expires'];
            }
            $structure->profile_id = $this->structure['profile_id'];
            $structure->position_x = $info['position']['x'];
            $structure->position_y = $info['position']['y'];
            $structure->position_z = $info['position']['z'];
            if(isset($this->structure['next_reinforce_apply'])) {
                $structure->next_reinforce_apply = $this->structure['next_reinforce_apply'];
            }
            if(isset($this->structure['next_reinforce_hour'])) {
                $structure->next_reinforce_hour = $this->structure['next_reinforce_hour'];
            }
            if(isset($this->structure['next_reinforce_weekday'])) {
                $structure->next_reinforce_weekday = $this->structure['next_reinforce_weekday'];
            }
            $structure->reinforce_hour = $this->structure['reinforce_hour'];
            if(isset($this->structure['reinforce_weekday'])) {
                $structure->reinforce_weekday = $this->structure['reinforce_weekday'];
            }
            if(isset($this->structure['unanchors_at'])) {
                $structure->unanchors_at = $this->structure['unanchors_at'];
            }            
            //If we set the structure services to true, let's save the services
            if($structure->services == true) {
                $this->StorestructureServices($this->structure['services'], $this->structure['structure_id']);
            }

            //Save the database record
            $structure->save();
        }

        //Record the structure's services information into the database

    }

    private function StoreStructureServices($services, $structureId) {
        foreach($services as $service) {
            //Find the structure id and the name of the service to see if it exists
            $found = Service::where([
                'structure_id' => $structureId,
                'name' => $service['name'],
            ])->get();

            if(!$found) {
                $new = new Service;
                $new->structure_id = $structureId;
                $new->name = $service['name'];
                $new->state = $service['state'];
                $new->save();
            } else {
                Service::where([
                    'structure_id' => $structureId,
                    'name' => $service['name'],
                ])->update([
                    'state' => $service['state'],
                ]);
            }

            
        }
    }

    private function DecodeDate($date) {
        $esiHelper = new Esi();

        $dateTime = $esiHelper->DecodeDate($date);

        return $dateTime;
    }

    private function HasEsiScope() {
        $esiHelper = new Esi();

        $universe = $esi->HaveEsiScope($this->charId, 'esi-universe.read_structures.v1');
        
        if($universe == true) {
            return true;
        } else {
            return false;
        }
    }
}
