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
        $struct = new Structure;
        $struct->structure_id = $structure->structure_id;
        $struct->structure_name = $info->name;
        $struct->corporation_id = $info->owner_id;
        $struct->solar_system_id = $info->solar_system_id;
        $struct->solary_system_name = $solarName;
        if(isset($info->type_id)) {
            $struct->type_id = $info->type_id;
        }
        $struct->corporation_id = $structure->corporation_id;
        if(isset($structure->services)) {
            $struct->services = true;
        } else {
            $struct->services = false;
        }
        if(isset($structure->state_timer_start)) {
            $struct->state_timer_start = $this->DecodeDate($structure->state_timer_start);
        }
        if(isset($structure->state_timer_end)) {
            $struct->state_timer_end = $this->DecodeDate($structure->state_timer_end);
        }
        if(isset($structure->fuel_expires)) {
            $struct->fuel_expires = $structure->fuel_expires;
        }
        $struct->profile_id = $structure->profile_id;
        $struct->position_x = $info->position->x;
        $struct->position_y = $info->position->y;
        $struct->position_z = $info->position->z;
        if(isset($structure->next_reinforce_apply)) {
            $struct->next_reinforce_apply = $structure->next_reinforce_apply;
        }
        if(isset($structure->next_reinforce_hour)) {
            $struct->next_reinforce_hour = $structure->next_reinforce_hour;
        }
        if(isset($structure->next_reinforce_weekday)) {
            $struct->next_reinforce_weekday = $structure->next_reinforce_weekday;
        }
        $struct->reinforce_hour = $structure->reinforce_hour;
        if(isset($structure->reinforce_weekday)) {
            $struct->reinforce_weekday = $structure->reinforce_weekday;
        }
        if(isset($structure->unanchors_at)) {
            $struct->unanchors_at = $structure->unanchors_at;
        }
        //Save the database record
        $struct->save();
        
        //If we set the structure services to true, let's save the services
        if($structure->services == true) {
            $this->StorestructureServices($structure->services, $structure->structure_id);
        }

        
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