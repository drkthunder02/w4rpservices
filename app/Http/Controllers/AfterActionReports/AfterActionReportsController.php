<?php

namespace App\Http\Controllers\AfterActionReports;

//Internal Library
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

//Models
use App\Models\AfterActionReports\AfterActionReport;
use App\Models\AfterActionReports\AfterActionReportComment;

class AfterActionReportsController extends Controller
{
    public function __contstruct() {
        $this->middleware('auth');
        $this->middleware('permission:fc.team');
    }

    public function DisplayReportForm() {
        return view('reports.user.form.report');
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
        $report->additonal_comments = $request->comments;
        $report->save();
        
        return redirect('/reports/display/all')->with('success', 'Added report to the database.');
    }

    public function DisplayCommentForm($id) {
        return view('reports.user.form.comment')->with('id', $id);
    }

    public function StoreComment(Request $request) {
        $this->validate($request, [
            'reportId' => 'required',
            'comments' => 'required',
        ]);

        $comment = new AfterActionReportComment;
        $comment->report_id = $request->reportId;
        $comment->character_id = auth()->user()->getId();
        $comment->character_name = auth()->user()->getName();
        $comment->comments = $required->comments;
        $comment->save();

        return redirect('/reports/display/all')->with('success', 'Added comemnt to the report.');
    }

    public function DisplayAllReports() {
        //Grab all the reports
        $reports = AfterActionReports::where('created_at', '>=', Carbon::now()->subDays(30));
        $comments = AfterActionReportComment::where('created_at', '>=', Carbon::now()->subDays(30));
        
        return view('reports.user.displayreports')->with('reports', $reports)
                                                  ->with('comments', $comments);
    }
}
