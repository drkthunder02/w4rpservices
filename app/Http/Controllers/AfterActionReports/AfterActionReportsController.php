<?php

namespace App\Http\Controllers\AfterActionReports;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            'location' => 'required',
            'time' => 'required',
            'comms' => 'required',
            'doctrine' => 'required',
            'objective' => 'required',
            'result' => 'required',
            'summary' => 'required',
            'improvements' => 'required',
            'well' => 'required',
            'comments' => 'required',
        ]);

        $report = new Report;
        $report->fc_id = auth()->user()->getId();
        $report->fc_name = auth()->user()->getName();
        $report->formup_time = $request->time;
        $report->formup_location = $request->location;
        $report->comms = $request->comms;
        $report->doctrine = $request->doctrine;
        $report->objective = $request->objective;
        $report->objective_result = $request->result;
        $report->summary = $request->summary;
        $report->improvements = $request->improvements;
        $report->worked_well = $request->well;
        $report->additon_comments = $request->comments;
        $report->save();
        
        return redirect('/reports/display/all')->with('success', 'Added report to the database.');
    }

    public function StoreComment(Request $request) {
        $this->validate($request, [
            'report_id' => 'required',
            'comments' => 'required',
        ]);

        $comment = new AfterActionReportComment;
        $comment->report_id = $request->report_id;
        $comment->character_id = auth()->user()->getId();
        $comment->character_name = auth()->user()->getName();
        $comment->comments = $required->comments;
        $comment->save();

        return redirect('/reports/display/all')->with('success', 'Added comemnt to the report.');
    }

    public function DisplayAllReports() {
        //Declare variables
        $comments = array();

        //Grab all the reports
        $reports = AfterActionReports::where('created_at', '>=', Carbon::now()->subDays(30));
        


        return view('reports.user.displayreports');
    }
}
