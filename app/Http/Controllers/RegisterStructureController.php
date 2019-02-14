<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;

use App\Models\Corporation\CorpStructure;
use App\Models\Corporation\CorpTaxRatio;
use App\Library\Esi\Esi;

class RegisterStructureController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:User');
        $this->middleware('permission:structure.operator');
    }

    public function displayRegisterTaxRatio() {
        $this->middleware('role:Admin');
        
        return view('structures.register.taxratio');
    }

    public function storeTaxRatio(Request $request) {
        $this->validate($request, [
            'corpId',
            'corporation',
            'type',
            'ratio',
        ]);

        $ratio = new CorpTaxRatio;
        $ratio->corporation_id = $request->corpId;
        $ratio->corporation_name = $request->corporation;
        $ratio->structure_type = $request->type;
        $ratio->ratio = $request->ratio;
        $ratio->save();

        return redirect('structure.admin.dashboard');
    }

    public function updateTaxRatio(Request $request) {
        $this->validate($request, [
            'corporation',
            'type',
            'ratio',
        ]);

        CorpTaxRatio::where([
            'corporation_name' => $request->corporation,
            'structure_type' => $request->type,
        ])->update([
            'ratio' => $request->ratio,
        ]);

        return redirect('structure.admin.dashboard')->with('success', 'Tax Ratio updated for structure type: ' . $request->type . ' and corporation: ' . $request->corporation);
    }

    public function displayTaxRatios() {
        $taxRatios = CorpTaxRation::all();

        return view('structure.admin.taxratios')->with('structures', $structures);
    }

    public function displayRegisterStructure() {
        //Check to see if the user has the read corp journal esi scope before allowing to register a structure
        if(Auth()->user()->hasEsiScope('esi-wallet.read_corporation_wallets.v1')) {
            return view('structures.register.register');
        } else {
            return view('dashboard')->with('error', 'You need to setup your esi scope for read corporation wallets');
        }
    }

    public function storeStructure(Request $request) {
        $this->validate($request, [
            'system' => 'required',
            'structure_name' => 'required',
            'tax' => 'required',
            'structure_type' => 'required',
        ]);

        $eHelper = new Esi;

        $tax = floatval($request->tax);

        $structure = new CorpStructure();
        $structure->character_id = Auth::user()->character_id;
        $structure->corporation_id = $eHelper->FindCorporationId(Auth::user()->character_id);
        $structure->corporation_name = $eHelper->FindCorporationName(Auth::user()->character_id);
        $structure->region = $request->region;
        $structure->system = $request->system;
        $structure->structure_name = $request->structure_name;
        $structure->tax = $tax;
        $structure->structure_type = $request->structure_type;
        $structure->save();

        //Return the view and the message of user updated
        return redirect('/dashboard')->with('success', 'Structure Added to Database');
    }
}
