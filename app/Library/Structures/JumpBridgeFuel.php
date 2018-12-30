<?php

namespace App\Library\Structures;

use App\Library\Esi\Esi;

use App\Models\Esi\EsiScope;
use App\Models\Esi\EsiToken;

use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\RequestFailedException;

class JumpBridgeFuel {

    private $charId;
    private $corpId;
    private $hasScopes;

    public function construct($charId, $corpId) {
        $this->charId = $charId;
        $this->corpId = $corpId;

        //Set ESI Scopes true or false whether we have the correct ones
        $esi = new Esi();
        if($esi->HaveEsiScope($this->charId, 'esi-assets.read_corporation_assets.v1') && 
           $esi->HaveEsiScope($this->charId, 'esi-corporations.read_structures.v1')) {
               $this->hasScopes = true;
           } else {
               $this->hasScopes = false;
           }
    }

    public function GetCorrrectScopes() {
        return $this->hasScopes;
    }

    public function GetStructureFuel() {
        
    }

    private function GetStructures($charId, $corpId) {
        //Delcare the data array for returning
        $data = array();

        //Get a list of structures.
        $config = config('esi');
        //Get the token from the database
        $token = EsiToken::where(['character_id' => $charId])->get(['refresh_token']);
        //Setup the ESI authentication container
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,

        ]);
        //Setup the ESI authentication container
        $esi = new Eseye($authentication);

        //set the ESI version we need to work with
        $esi->setVersion('v3');

        //Set our current page
        $currentPage = 1;
        //Set our default total pages, and will refresh this later
        $totalPages = 1;

        //If more than one page is found, decode the first, then the second
        do {
            //Try to gather the structures from ESI
            try {
                $structures = $esi->page($currentPage)
                                  ->invoke('get', '/corporations/{corporation_id}/structures/', [
                    'corporation_id' => $corpId,
                ]);
            } catch(RequestFailedException $e) {
                return null;
            }

            //Set the actual total pages after we performed the esi call
            $totalPages = $structures->pages;

            foreach($structures as $structure) {
                if($structure->type_id == 35841) {
                    $data = array_push($data, $structure);
                }
            }
        } while ($currentPage < $totalPages);

        //Add structures to a data array for just jump bridge type, and return the data array
        return $data;
    }

    private function GetAssets($corpId, $structures) {
        //Delcare the data array for returning
        $data = array();

        //Get a list of structures.
        $config = config('esi');
        //Get the token from the database
        $token = EsiToken::where(['character_id' => $charId])->get(['refresh_token']);
        //Setup the ESI authentication container
        $authentication = new EsiAuthentication([
            'client_id' => $config['client_id'],
            'secret' => $config['secret'],
            'refresh_token' => $token[0]->refresh_token,
        ]);
        //Setup the ESI authentication container
        $esi = new Eseye($authentication);

        //set the ESI version we need to work with
        $esi->setVersion('v3');

        //Set our current page
        $currentPage = 1;
        //Set our default total pages, and will refresh this later
        $totalPages = 1;

        //If more than one page is available we want to get all the pages
        do {
            try {
                //Try to pull the data from ESI
                $assets = $esi->page($currentPage)
                              ->invoke('get', '/corporations/{corporation_id}/assets/', [
                                  'corporation_id' => $corpId,
                              ]);
            } catch(RequestFailedException $e) {
                //If ESI fails, we just return null
                return null;
            }

            //Set the total number of pages
            $totalPages = $assets->pages;

            //For each entry, we only want to save the entries 
            foreach($assets as $asset) {
                if($asset->type_id == 16273) {
                    //If the type id is correct then push the data onto the array
                    $data = array_push($data, $asset);
                }
            }

        } while($currentPage < $totalPages);

        //Return the list of assets, the structure the asset is in, and the division,
        //to the  calling function
        return $data;
    }


}

?>