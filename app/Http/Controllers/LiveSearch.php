<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class LiveSearch extends Controller
{
    public function index()  {
        return view('ajax.live_search');
    }

    public function action(Request $request) {
        return response()->json(['success' => 'Data has been successfully added.']);
    }
}
