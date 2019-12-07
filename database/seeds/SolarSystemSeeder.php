<?php

use Illuminate\Database\Seeder;

use App\Models\Lookups\SolarSystem;

use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class SolarSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        $esi = new Eseye();

        $systems = $esi->invoke('get', '/universe/systems/');

        foreach($systems as $system) {
            try {
                $info = $esi->invoke('get', '/universe/systems/{system_id}/', [
                    'system_id' => $system,
                ]);
            } catch(RequestFailedException $e) {

            }
            
            $count = SolarSystem::where(['solar_system_id' => $system])->count();
            if($count == 0) {
                SolarSystem::insert([
                    'name' => $info->name,
                    'solar_system_id' => $system,
                    'security_status' => $info->security_status,
                ]);
            }
        }
    }
}
