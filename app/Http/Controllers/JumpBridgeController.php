<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Library\Finances\JumpBridgeTax;

class JumpBridgeController extends Controller
{
    /**
     * Displays all statistics on one page
     */
    public function displayAll() {
        //Create a helper class variable
        $jbHelper30 = new JumpBridgeTax(30);
        $jbHelper60 = new JumpBridgeTax(60);
        $jbHelper90 = new JumpBridgeTax(90);

        $data = [
            '30days' => number_format($jbHelper30->OverallTax(), 2, '.', ','),
            '60days' => number_format($jbHelper60->OverallTax(), 2, '.', ','),
            '90days' => number_format($jbHelper90->OverallTax(), 2, '.', ','),
        ];

        return view('jumpbridges.all')->with('data', $data);
    }

    /**
     * Displays overall jump bridge usage based corporation data
     */
    public function displayCorpUsage() {
        return view('jumpbridges.corp.select');
    }

    public function ajaxCorpUsage() {
        //Get the statistics for overall usage by corps and send back to webpage via ajax

        return response()->json(array('data' => $data), 200);
    }

    /**
     * Displays jump bridge usage based on structure
     */
    public function displayStructureUsage() {
        return view('jumpbridges.structure.select');
    }

    public function ajaxStructureUsage() {
        //Get the statistics for overall usage by structure and send back to the webpage via ajax

        return response()->json(array('data' => $data), 200);
    }
}
