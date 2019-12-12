<?php

use Illuminate\Database\Seeder;

use App\Models\Lookups\SolarSystem;

use Seat\Eseye\Exceptions\RequestFailedException;
use App\Library\Esi\Esi;

class SolarSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Declare some variables
        $esiHelper = new Esi;

        $esi = $esiHelper->SetupEsiAuthentication();

        $systems = $esi->invoke('get', '/universe/systems/');

        foreach($systems as $system) {
            try {
                $info = $esi->invoke('get', '/universe/systems/{system_id}/', [
                    'system_id' => $system,
                ]);
            } catch(RequestFailedException $e) {
                return null;
            }
            
            $count = SolarSystem::where(['solar_system_id' => $system])->count();
            if($count == 0) {
                SolarSystem::insert([
                    'name' => $info->name,
                    'solar_system_id' => $system,
                ]);
            }
        }
    }
}
