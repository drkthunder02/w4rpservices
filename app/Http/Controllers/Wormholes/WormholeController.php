<?php

namespace App\Http\Controllers\Wormholes;

//Laravel Libraries
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

//User Libraries

//Models

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
            'date' => 'required',
            'class' => 'required',
            'size' => 'required',
            'stability' => 'required',
        ]);
    }

    public function displayWormholes() {

    }
}
