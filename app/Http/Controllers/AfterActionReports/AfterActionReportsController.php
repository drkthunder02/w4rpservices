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
        //
    }

    public function StoreReport() {
        
    }

    public function StoreComment() {

    }

    public function DisplayAllReports() {

    }
}
