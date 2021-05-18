<?php

namespace App\Jobs\Commands\Structures;

//Internal Library
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//Application Library
use App\Library\Helpers\LookupHelper;
use App\Library\Esi\Esi;

//Jobs
use App\Jobs\Commands\Structures\ProcessAllianceStructureServices;

//Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;

class ProcessAllianceStructures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $structure;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($s)
    {
        //Set variables
        $this->structure = $s;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Update the structure if it already exists, or add the structure if it doesn't exist in the database
         */
        if(Structure::where(['structure_id' => $this->structure->structure_id])->count() > 0) {
            $this->UpdateStructure($this->structure);
        } else {
            $this->SaveNewStructure($this->structure);
        }
    }

    /**
     * Set the tags for the job
     * 
     * @var array
     */
    public function tags() {
        return ['ProcessAllianceStructures', 'AllianceStructures', 'Structures'];
    }

    private function SaveNewStructure($structure) {
        //Declare variables
        $lookup = new LookupHelper;
        $esiHelper = new Esi;

        //Get the solar system name
        $solarName = $lookup->SolarSystemIdToName($structure->system_id);

        $s = new Structure;
        $s->structure_id = $structure->structure_id;
        $s->structure_name = $structure->name;
        $s->solar_system_id = $structure->system_id;
        $s->solar_system_name = $solarName;
        $s->type_id = $structure->type_id;
        $s->type_name = $lookup->StructureTypeIdToName($structure->type_id);
        $s->corporation_id = $structure->corporation_id;
        if(isset($structure->services)) {
            $s->services = true;
        } else {
            $s->services = false;
        }
        $s->state = $structure->state;
        if(isset($structre->state_timer_start)) {
            $s->state_timer_start = $esiHelper->DecodeDate($structure->state_timer_start);
        }
        if(isset($structure->state_timer_end)) {
            $s->state_timer_end = $esiHelper->DecodeDate($structure->state_timer_end);
        }
        if(isset($structure->fuel_expires)) {
            $s->fuel_expires = $esiHelper->DecodeDate($structure->fuel_expires);
        }
        $s->profile_id = $structure->profile_id;
        if(isset($structure->next_reinforce_apply)) {
            $s->next_reinforce_apply = $structure->next_reinforce_apply;
        }
        if(isset($structure->next_reinforce_hour)) {
            $s->next_reinforce_hour = $structure->next_reinforce_hour;
        }
        $s->reinforce_hour = $structure->reinforce_hour;
        if(isset($structure->unanchors_at)) {
            $s->unanchors_at = $esiHelper->DecodeDate($s->unanchors_at);
        }
        $s->save();

        //If there are services on the structure, then save the service and the structure id in the database table.
        if($s->services == true) {
            foreach($structure->services as $service) {
                $serv = new Service;
                $serv->structure_id = $structure->structure_id;
                $serv->name = $service->name;
                $serv->state = $service->state;
            }
        }
    }

    private function UpdateStructure($structure) {
        if(isset($structure->corporation_id)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'corporation_id' => $structure->corporation_id,
            ]);
        }
        if(isset($structure->state)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'state' => $structure->state,
            ]);
        }
        if(isset($structure->state_timer_start)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'state_timer_start' => $structure->state_timer_start,
            ]);
        }
        if(isset($structure->state_timer_end)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'state_timer_end' => $structure->state_timer_end,
            ]);
        }
        if(isset($structure->fuel_expires)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'fuel_expires' => $structure->fuel_expires,
            ]);
        }
        if(isset($structure->profile_id)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'profile_id' => $structure->profile_id,
            ]);
        }
        if(isset($structure->next_reinforce_apply)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'next_reinforce_apply' => $structure->next_reinforce_apply,
            ]);
        }
        if(isset($structure->next_reinforce_hour)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'next_reinforce_hour' => $structure->next_reinforce_hour,
            ]);
        }
        if(isset($structure->reinforce_hour)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'reinforce_hour' => $structure->reinforce_hour,
            ]);
        }
        if(isset($structure->unanchors_at)) {
            Structure::where([
                'structure_id' => $structure->structure_id,
            ])->update([
                'unanchors_at' => $structure->unanchors_at,
            ]);
        }

        if(Service::where(['structure_id' => $structure->structure_id])->count() > 0) {
            foreach($structure->services as $service) {
                Service::where([
                    'structure_id' => $structure->structure_id,
                ])->update([
                    'state' => $service->state,
                ]);
            }
        }
    }
}
