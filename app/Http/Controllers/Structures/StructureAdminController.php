<?php

namespace App\Http\Controllers\Structures;

use Illuminate\Http\Request;

class StructureAdminController extends Controller
{
    public function __construct() {
        $this->middleware('role:Admin');
    }

    public function displayDashboard() {
        return view('structures.admin.dashboard');
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

        return redirect('/structure/admin/dashboard')->with('success', 'Tax Ratio updated for structure type: ' . $request->type . ' and corporation: ' . $request->corporation);
    }

    public function displayTaxRatios() {
        $taxRatios = CorpTaxRation::all();

        return view('structure.admin.taxratios')->with('structures', $structures);
    }
}
