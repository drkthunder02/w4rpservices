<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Structures;

//Internal Library
use Log;
use DB;
use Carbon\Carbon;

//App Models
use App\Models\Structure\Structure;
use App\Models\Structure\Service;


class StructureHelper {

    public function StoreNewStructure($structure, $info, $solarName) {
        $st = new Structure;
        $st->structure_id = $structure->structure_id;
        $st->structure_name = $info->name;
        $st->corporation_id = $info->owner_id;
        $st->solar_system_id = $info->solar_system_id;
        $st->solary_system_name = $solarName;
        if(isset($info->type_id)) {
            $st->type_id = $info->type_id;
        }
        $st->corporation_id = $structure->corporation_id;
        if(isset($structure->services)) {
            $st->services = true;
        } else {
            $st->services = false;
        }
        if(isset($structure->state_timer_start)) {
            $st->state_timer_start = $this->DecodeDate($structure->state_timer_start);
        }
        if(isset($structure->state_timer_end)) {
            $st->state_timer_end = $this->DecodeDate($structure->state_timer_end);
        }
        if(isset($structure->fuel_expires)) {
            $st->fuel_expires = $structure->fuel_expires;
        }
        $st->profile_id = $structure->profile_id;
        $st->position_x = $info->position->x;
        $st->position_y = $info->position->y;
        $st->position_z = $info->position->z;
        if(isset($structure->next_reinforce_apply)) {
            $st->next_reinforce_apply = $structure->next_reinforce_apply;
        }
        if(isset($structure->next_reinforce_hour)) {
            $st->next_reinforce_hour = $structure->next_reinforce_hour;
        }
        if(isset($structure->next_reinforce_weekday)) {
            $st->next_reinforce_weekday = $structure->next_reinforce_weekday;
        }
        $st->reinforce_hour = $structure->reinforce_hour;
        if(isset($structure->reinforce_weekday)) {
            $st->reinforce_weekday = $structure->reinforce_weekday;
        }
        if(isset($structure->unanchors_at)) {
            $st->unanchors_at = $structure->unanchors_at;
        }
        //Save the database record
        //$st->save();
        DB::table('AllianceStructures')->insert($st);
        
        /*
        //If we set the structure services to true, let's save the services
        if($structure->services == true) {
            $this->StorestructureServices($structure->services, $structure->structure_id);
        }
        */

        
    }

    public function UpdateExistingStructure($structure, $info, $solarName) {
        //For each line see if it is part of the structure array, and attempt to modify each variable
        //This will be implemented in the near future.

        if(isset($structure->services)) {
            foreach($structure->services as $service) {
                //Search for the service, and if found, update it, else add it.
                $serviceFound = Service::where([
                    'structure_id' => $structure->structure_id,
                    'name' => $service->name,
                ])->get();
                if($serviceFound) {
                    Service::where([
                        'structure_id' => $structure->structure_id,
                        'name' => $service->name,
                    ])->update([
                        'state' => $service->state,
                    ]);
                } else {
                    $newService = new Service;
                    $newService->structure_id = $structure->structure_id;
                    $newService->name = $service->name;
                    $newService->state = $service->state;
                }
                
            }
        }
    }

    public function StoreStructureServices($services, $structureId) {
        foreach($services as $service) {
            //Find the structure id and the name of the service to see if it exists
            $found = Service::where([
                'structure_id' => $structureId,
                'name' => $service->name,
            ])->get();

            if(!$found) {
                $new = new Service;
                $new->structure_id = $structureId;
                $new->name = $service->name;
                $new->state = $service->state;
                $new->save();
            } else {
                Service::where([
                    'structure_id' => $structureId,
                    'name' => $service->name,
                ])->update([
                    'state' => $service->state,
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

?>