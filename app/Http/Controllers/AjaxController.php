<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use App\Library\Fleets;

use App\Models\Fleet\Fleet;

class AjaxController extends Controller {

    public function index() {
        $msg = "This is a simple message.";
        return response()->json(array('msg'=> $msg), 200);
    }

    public function displayFleet() {
        $fleets = Fleet::all();
        $data = array();
        $fc = array();
        $fleet = array();
        $description = array();
        $i = 0;

        foreach($fleets as $fl) {
            $fc[$i] = $fl->character_id;
            $fleet[$i] = $fl->fleet;
            $description[$i] = $fl->description;
            $i++;
        }

        $data = [
            $fc,
            $fleet,
            $description,
        ];

        return response()->json(array('data' => $data), 200);
    }
}