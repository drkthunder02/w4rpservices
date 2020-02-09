<?php

namespace App\Http\Controllers\Wormholes;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;

//User Libraries

//Models
use App\Models\Wormholes\AllianceWormhole;
use App\Models\Wormholes\WormholeType;

class WormholeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
    }

    public function displayWormholeForm() {
        //Declare a few array variables
        $duration = array();
        $class = array();
        $stability = array();
        $size = array();

        //Get the duration from the table
        $duration = [
            'This wormhole has not yet begun its natural cycle of decay and should last at least another day.',
            'This wormhole is beginning to decay, but will not last another day.',
            'This wormhole is reaching the end of its natural lifetime',
        ];

        //Get the wh classes from the table
        $class = [
            'C1',
            'C2',
            'C3',
            'C4',
            'C5',
            'C6',
            'C7',
            'C8',
            'C9',
            'C13',
            'Drifter',
            'Thera',
            'Exit WH',
        ];

        //Get the wh types from the table
        $type = WormholeType::pluck('type');

        //Get the wh sizes from the table
        $size = [
            'XS',
            'S',
            'M',
            'L',
            'XL',
        ];

        //Get the wh stabilities from the table
        $stability = [
            'Stable',
            'Non-Critical',
            'Critical',
        ];

        //Return all the variables to the view
        return view('wormholes.form')->with('class', $class)
                                     ->with('type', $type)
                                     ->with('size', $size)
                                     ->with('stability', $stability)
                                     ->with('duration', $duration);
    }

    public function storeWormhole() {
        $this->validate($request, [
            'sig' => 'required',
            'duration' => 'required',
            'dateTiume' => 'required',
            'class' => 'required',
            'size' => 'required',
            'stability' => 'required',
            'type' => 'required',
            'system' => 'required',
        ]);

        //Declare some variables
        $duration = null;

        //Create the stable time for the database
        if($request->duration == 'This wormhole has not yet begun its natural cycle of decay and should last at least another day.') {
            $duration = '>24 hours';
        } else if ($request->duration == 'This wormhole is beginning to decay, but will not last another day.') {
            $duration = '>4 hours <24 hours';
        } else if($request->duration == 'This wormhole is reaching the end of its natural lifetime') {
            '<4 hours';
        }

        //Get the wormhole type from the database so we can enter other details
        $wormholeType = WormholeType::where([
            'type' => $request->type,
        ])->first();

        $found = AllianceWormhole::where([
            'system' => $request->system,
            'sig_ig' => $request->sig,
        ])->count();

        if($found == 0) {
            AllianceWormhole::insert([
                'system' => $request->system,
                'sig_id' => $request->sig_id,
                'duration_left' => $duration,
                'dateTime' => $request->dateTime,
                'class' => $request->class,
                'type' => $request->type,
                'hole_size' => $request->size,
                'stability' => $request->stability,
                'details' => $request->details,
                'link' => $request->link,
                'mass_allowed' => $wormholeType->mass_allowed,
                'individual_mass' => $wormholeType->individual_mass,
                'regeneration' => $wormholeType->regeneration,
                'max_stable_time' => $wormholeType->max_stable_time,
            ]);

            return redirect('/wormholes/display')->with('success', 'Wormhole Info Added.');
        } else {
            return redirect('/wormholes/display')->with('error', 'Wormhole already in database.');
        }
    }

    public function displayWormholes() {
        //Create the date and time
        $dateTime = Carbon::now()->subDays(2);

        //Get all of the wormholes from the last 48 hours from the database to display
        $wormholes = AllianceWormholes::where('created_at', '>=', $dateTime)->get();

        return view('wormholes.display')->with('wormholes', $wormholes);
    }
}
