<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Library\Lookups\LookupHelper;

class TestController extends Controller
{
    public function displayCharTest() {
        $lookup = new LookupHelper;

        $char = $lookup->GetCharacterInfo(93738489);

        return view('test.char.display')->with('char', $char);
    }

    public function CharacterLookupTest(Request $request) {
        
    }
}
