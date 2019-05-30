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
use App\Models\Jobs\JobStatus;
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
    private $page;
    private $esi;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobProcessStructure $jps)
    {
        $this->charId = $jps->charId;
        $this->corpId = $jps->corpId;
        $this->page = $jps->page;
        $this->esi = $jps->esi;

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
        //Get the page of structures
        $structures = $this->GetListOfStructures();

        foreach($structures as $structure) {
            $info = $this->GetStructureInfo($structure['structure_id']);

            $solarName = $this->GetSolarSystemName($info['solar_system_id']);


            //Record the structure information into the database
            //Find if the structure exists
            $found = Structure::where(['structure_id' => $structure['structure_id']])->get();
            if($found) {
                $this->UpdateExistingStructure($structure, $info, $solarName);
            } else {
                $this->StoreNewStructure($structure, $info, $solarName);
            }
        }
    }

    private function UpdateExistingStructure($structure, $info, $solarName) {
        //For each line see if it is part of the structure array, and attempt to modify each variable
        //This will be implemented in the near future.

        if(isset($structures['services'])) {
            foreach($structure['service'] as $service) {
                //Search for the service, and if found, update it, else add it.
                $serviceFound = Service::where([
                    'structure_id' => $structure['structure_id'],
                    'name' => $service['name'],
                ])->get();
                if($serviceFound) {
                    Service::where([
                        'structure_id' => $structure['structure_id'],
                        'name' => $service['name'],
                    ])->update([
                        'state' => $service['state'],
                    ]);
                } else {
                    $newService = new Service;
                    $newService->structure_id = $structure['structure_id'];
                    $newService->name = $service['name'];
                    $newService->state = $service['state'];
                }
                
            }
        }
    }

    private function StoreNewStructure($structure, $info, $solarName) {
        $structure = new Structure;
        $structure->structure_id = $structure['structure_id'];
        $structure->structure_name = $info['name'];
        $structure->corporation_id = $info['owner_id'];
        $structure->solar_system_id = $info['solar_system_id'];
        $structure->solary_system_name = $solarName;
        if(isset($info['type_id'])) {
            $structure->type_id = $info['type_id'];
        }
        $structure->corporation_id = $structure['corporation_id'];
        if(isset($structures['services'])) {
            $structure->services = true;
        } else {
            $structure->services = false;
        }
        if(isset($structure['state_timer_start'])) {
            $structure->state_timer_start = $this->DecodeDate($structure['state_timer_start']);
        }
        if(isset($structure['state_timer_end'])) {
            $structure->state_timer_end = $this->DecodeDate($structure['state_timer_end']);
        }
        if(isset($structure['fuel_expires'])) {
            $structure->fuel_expires = $structure['fuel_expires'];
        }
        $structure->profile_id = $structure['profile_id'];
        $structure->position_x = $info['position']['x'];
        $structure->position_y = $info['position']['y'];
        $structure->position_z = $info['position']['z'];
        if(isset($structure['next_reinforce_apply'])) {
            $structure->next_reinforce_apply = $structure['next_reinforce_apply'];
        }
        if(isset($structure['next_reinforce_hour'])) {
            $structure->next_reinforce_hour = $structure['next_reinforce_hour'];
        }
        if(isset($structure['next_reinforce_weekday'])) {
            $structure->next_reinforce_weekday = $structure['next_reinforce_weekday'];
        }
        $structure->reinforce_hour = $structure['reinforce_hour'];
        if(isset($structure['reinforce_weekday'])) {
            $structure->reinforce_weekday = $structure['reinforce_weekday'];
        }
        if(isset($structure['unanchors_at'])) {
            $structure->unanchors_at = $structure['unanchors_at'];
        }            
        //If we set the structure services to true, let's save the services
        if($structure->services == true) {
            $this->StorestructureServices($structure['services'], $structure['structure_id']);
        }

        //Save the database record
        $structure->save();
    }

    private function GetSolarSystemName($systemId) {
        //Attempt to get the solar system name from ESI
        try {
            $solarName = $this->esi->invoke('get', '/universe/systems/{system_id}/', [
                'system_id' => $systemId,
            ]);
        } catch(RequestFailedException $e) {
            $solarName = null;
        }

        return $solarName;
    }

    private function GetStructureInfo($structureId) {
        try {
            $info = $this->esi->invoke('get', '/universe/structures/{structure_id}/', [
                'structure_id' => $structureId,
            ]);
        } catch(RequestFailedException $e) {
            $info = null;
        }

        return $info;
    }

    private function GetListOfStructures() {
        try {
            $structures = $this->esi->page($this->page)
                              ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                'corporation_id' => $this->corpId,
                                ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get structure list.");
            $structures = null;
        }

        return $structures;
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
}