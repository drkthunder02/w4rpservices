<?php

namespace App\Http\Controllers\AfterActionReports;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Models
use App\Models\AfterActionReports\Report;
use App\Models\AfterActionReports\Comment;

class AfterActionReportsController extends Controller
{
    public function __contstruct() {
        $this->middleware('auth');
        $this->middleware('permission:fc.team');
    }

    public function DisplayReportForm() {
        return view('reports.user.displayform');
    }

    public function StoreReport(Request $request) {
        $this->validate($request, [

        ]);
    }

    public function StoreComment(Request $request) {
        $this->validate($request, [

        ]);
    }

    public function DisplayAllReports() {
        

        return view('reports.user.displayreports');
    }
}
