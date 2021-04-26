<?php

/**
 * W4RP Services
 * GNU Public License
 */

namespace App\Library\Helpers;

//Internal Library
use Log;

//App Library
use App\Jobs\Library\JobHelper;
use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;
use App\Library\Lookups\LookupHelper;

//App Models
use App\Models\Jobs\JobStatus;
use App\Models\Structure\Structure;
use App\Models\Structure\Service;
use App\Models\Esi\EsiToken;
use App\Models\Esi\EsiScope;


class StructureHelper {

    private $charId;
    private $corpId;
    private $page;
    private $esi;

    public function __construct($char, $corp, $esi = null) {
        $this->charId = $char;
        $this->corpId = $corp;
        if($esi == null) {
            $esiHelper = new Esi;
            $token = $esiHelper->GetRefreshToken($char);
            $this->esi = $esiHelper->SetupEsiAuthentication($token);
        } else {
            $this->esi = $esi;
        }       
    }

    public function GetStructuresByPage($page) {
        
        //Try to get the ESI data
        try {
            $structures = $this->esi->page($page)
                              ->invoke('get', '/corporations/{corporation_id}/structures/', [
                                'corporation_id' => $this->corpId,
                                ]);
        } catch (RequestFailedException $e) {
            Log::critical("Failed to get structure list.");
            return null;
        }

        return $structures;
    }

    public function ProcessStructure($structure) {
        
        //Get the structure information
        $info = $this->GetStructureInfo($structure->structure_id);

        //Record the structure information into the database
        //Find if the structure exists
        if(Structure::where(['structure_id' => $structure->structure_id])->count() == 0) {
            $this->SaveNewStructure($structure, $info);            
        } else {
            $this->UpdateExistingStructure($structure, $info);
        }
    }

    public function GetSolarSystemName($systemId) {
        //Declare some variables
        $lookup = new LookupHelper;

        $solar = $lookup->SystemIdToName($systemId);

        if($solar != null) {
            return $solar;
        } else {
            return null;
        }
    }

    public function GetStructureName($structureId) {
        try {
            $info = $this->esi->invoke('get', '/universe/structures/{structure_id}/', [
                'structure_id' => $structureId,
            ]);
        } catch(RequestFailedException $e) {
            Log::warning("Failed to get structure information for structure with id " . $structureId);
            Log::warning($e->getCode());
            Log::warning($e->getMessage());
            Log::warning($e->getEsiResponse());
            $info = null;
        }

        $structure = json_decode($info->raw, true);

        if(!isset($structure['name'])) {
            return null;
        } else {
            return (string)$structure['name'];
        }
    }

    public function GetStructureInfo($structureId) {
        try {
            $info = $this->esi->invoke('get', '/universe/structures/{structure_id}/', [
                'structure_id' => $structureId,
            ]);
        } catch(RequestFailedException $e) {
            Log::warning("Failed to get structure information for structure with id " . $structureId);
            Log::warning($e->getCode());
            Log::warning($e->getMessage());
            Log::warning($e->getEsiResponse());
            $info = null;
        }

        return $info;
    }

    private function UpdateExistingStructure($structure, $info) {
        //Update the structure id and name
        Structure::where(['structure_id' => $structure->structure_id])->update([
            'structure_id' => $structure->structure_id,
            'structure_name' => $info->name,
        ]);

        //Update the services
        if(isset($structure->services)) {
            $services = true;
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'services' => $services,
            ]);
        } else {
            $services = false;
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'services' => $services,
            ]);
        }

        //Update the structure state
        if(isset($structure->state)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'state' => $structure->state,
            ]);
        } else {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'state' => 'None',
            ]);
        }

        //Update the state timer start
        if(isset($structure->state_timer_start)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'state_timer_start' => $this->DecodeDate($structure->state_timer_start),
            ]);
        }

        //Update the state timer end
        if(isset($structure->state_timer_end)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'state_timer_end' => $this->DecodeDate($structure->state_timer_end),
            ]);
        }

        //Update the fuel expires
        if(isset($structure->fuel_expires)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'fuel_expires' => $this->DecodeDate($structure->fuel_expires),
            ]);
        }

        //Update the profile id, and positions
        Structure::where(['structure_id' => $structure->structure_id])->update([
            'profile_id' => $structure->profile_id,
            'position_x' => $info->position->x,
            'position_y' => $info->position->y,
            'position_z' => $info->position->z,
        ]);

        //Update the next reinforce apply
        if(isset($structure->next_reinforce_apply)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'next_reinforce_apply' => $this->DecodeDate($structure->next_reinforce_apply),
            ]);
        }

        //update the next reinforce hour
        if(isset($structure->next_reinforce_hour)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'next_reinforce_hour' => $structure->next_reinforce_hour,
            ]);
        }

        //Update next reinforce weekday
        if(isset($structure->next_reinforce_weekday)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'next_reinforce_weekday' => $structure->next_reinforce_weekday,
            ]);
        }

        //Update reinforce hour
        if(isset($structure->reinforce_hour)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'reinforce_hour' => $structure->reinforce_hour,
            ]);
        }

        //Update reinforce weekday
        if(isset($structure->reinforce_weekday)) {
            Structure::where(['structure_id' => $structure->structure_id])->update([
                'reinforce_weekday' => $structure->reinforce_weekday,
            ]);
        }

        //Update the unanchors at field
        if(isset($structure->unanchors_at)) {
            //Decode the date / time
            $daTi = $this->DecodeDate($structure->unanchors_at);

            Structure::where(['structure_id' => $structure->structure_id])->update([
                'unanchors_at' => $daTi,
            ]);
        }

        //Update the services for the structure as well
        if($services == true) {
            //Delete the existing services, then add the new services
            if(Service::where(['structure_id' => $structure->structure_id])->count() > 0) {
                Service::where(['structure_id' => $structure->structure_id])->delete();
            }
            

            foreach($structure->services as $service) {
                $serv = new Service;
                $serv->structure_id = $structure->structure_id;
                $serv->name = $service->name;
                $serv->state = $service->state;
                $serv->save();
            }
        }
    }

    private function SaveNewStructure($structure, $info) {

        if(isset($info->solar_system_id)) {
            $solarName = $this->GetSolarSystemName($info->solar_system_id);
        } else {
            Log::critical("Couldn't get solar system name for structure " . $structure->structure_id);
            Log::critical("Check access lists.");
            $solarName = null;
        }

        $st = new Structure;
        $st->structure_id = $structure->structure_id;
        $st->structure_name = $info->name;
        $st->corporation_id = $info->owner_id;
        $st->solar_system_id = $info->solar_system_id;
        $st->solar_system_name = $solarName;
        if(isset($info->type_id)) {
            $st->type_id = $info->type_id;
        }
        $st->corporation_id = $structure->corporation_id;
        if(isset($structure->services)) {
            $st->services = true;
        } else {
            $st->services = false;
        }
        if(isset($structure->state)) {
            $st->state = $structure->state;
        } else {
            $st->state = 'None';
        }
        if(isset($structure->state_timer_start)) {
            $st->state_timer_start = $this->DecodeDate($structure->state_timer_start);
        }
        if(isset($structure->state_timer_end)) {
            $st->state_timer_end = $this->DecodeDate($structure->state_timer_end);
        }
        if(isset($structure->fuel_expires)) {
            $st->fuel_expires = $this->DecodeDate($structure->fuel_expires);
        }
        $st->profile_id = $structure->profile_id;
        $st->position_x = $info->position->x;
        $st->position_y = $info->position->y;
        $st->position_z = $info->position->z;
        if(isset($structure->next_reinforce_apply)) {
            $st->next_reinforce_apply = $this->DecodeDate($structure->next_reinforce_apply);
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
            $daTi = $this->DecodeDate($structure->unanchors_at);
            $st->unanchors_at = $daTi;
        }

        //Save the database record
        $st->save();

        if($st->services == true) {
            foreach($structure->services as $service) {
                $serv = new Service;
                $serv->structure_id = $structure->structure_id;
                $serv->name = $service->name;
                $serv->state = $service->state;
            }
        }
    }

    public function GetStructuresByType($type) {
        $sType = $this->StructureTypeToId($type);

        $structures = Structure::where([
            'type_id' => $sType,
        ])->get();

        return $structures;
    }

    private function StructureTypeToId($name) {
        $structureTypes = [
            'Ansiblex Jump Gate' => 35841,
            'Pharolux Cyno Beacon' => 35840,
            'Tenebrex Cyno Jammer' => 37534,
            'Keepstar' => 35834,
            'Fortizar' => 35833,
            'Astrahus' => 35832,
            'Tatara' => 35836,
            'Athanor' => 35835,
            'Sotiyo' => 35827,
            'Azbel' => 35826,
            'Raitaru' => 35825,
        ];

        return $structureTypes[$name];
    }

    private function DecodeDate($date) {
        $esiHelper = new Esi;

        $dateTime = $esiHelper->DecodeDate($date);

        return $dateTime;
    }
}

?>