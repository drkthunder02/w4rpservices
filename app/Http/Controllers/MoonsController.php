<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Moon;
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;

class MoonsController extends Controller
{
    /**
     * Add a new moon into the database
     * 
     * @return \Illuminate\Http\Reponse
     */
    public function addMoon(Request $request) {
        $this->validate($request, [
            'region' => 'required',
            'system' => 'required',
            'structure' => 'required',

        ]);

        // Add new moon
        $moon = new Moon;
        $moon->Region = $request->input('region');
        $moon->System = $request->input('system');
        $moon->StructureName = $request->input('structure');
        $moon->FirstOre = $request->input('firstore');
        $moon->FirstQuantity = $request->input('firstquan');
        $moon->SecondOre = $request->input('secondore');
        $moon->SecondQuantity = $request->input('secondquan');
        $moon->ThirdOre = $request->input('thirdore');
        $moon->ThirdQuantity = $request->input('thirdquan');
        $moon->FourthOre = $request->input('fourthore');
        $moon->FourthQuantity = $request->input('fourthquan');
        $moon->save();

        return redirect('/dashboard')->with('success', 'Moon Added');
    }

    /**
     * Returns a view with a table select for all of the structures in the corp owned by the player
     */
    public function moonminedisplay() {
        // Disable all caching by setting the NullCache as the
        // preferred cache handler. By default, Eseye will use the
        // FileCache.
        $configuration = Configuration::getInstance();
        $configuration->cache = NullCache::class;

        /**
         * Create the auth user space.
         * Get the character Id.
         * Check the character id against the esi token table
         * If the refresh token is available then request an ESI pull
         * If the refresh token is not available, display an error message
         */
        $user = Auth::user();
        $characterId = $user->getCharacterId();

        // Prepare an authentication container for ESI
        $authentication = new EsiAuthentication([
            'client_id'     => env('ESI_CLIENT_ID'),
            'secret'        => env('ESI_SECRET_KEY'),
            'refresh_token' => null,
        ]);

        // Instantiate a new ESI instance.
        $esi = new Eseye($authentication);

    }
}
