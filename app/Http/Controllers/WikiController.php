<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class WikiController extends Controller
{
    public function displayRegister() {
        return view('wiki.register');
    }

    public function storeRegister(Request $request) {
        $this->validate($request, [
            'password' => 'required',
            'password2' => 'required',
        ]);

        //Add the new user to the wiki
        
    }
}
