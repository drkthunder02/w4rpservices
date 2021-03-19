@extends('layouts.user.dashb4')
@section('content')
<br>
@if($reports != null)
@foreach($reports as $report)
<div class="card">
    <div class="card-header">
        <!-- Body of report goes here -->
        FC: {{ $report->fc_name }}<br>
        Formup: {{ $report->formup_location }}@{{ $report->formup_time }}<br>
        Comms: {{ $report->comms }}<br>
        Doctrine: {{ $report->doctrine }}<br>
        Objective: {{ $report->objective }}<br>
        Fleet Result: {{ $report->objective_result }}<br>
        Summary: {{ $report->summary }}<br>
        Improvements: {{ $report->improvements }}<br>
        Worked Well: {{ $report->worked_well }}<br>
        Additional Comments: {{ $report->additional_comments }}<br>
        <a class="btn btn-outline-dark" href="/reports/display/comment/form/{{$report->id}}" role="button">Click to Comment</a>
    </div>
    <div class="card-body">
        @foreach($comments as $comment)
        @if($comment->report_id == $report->id)
        Name: {{$comment->character_name }}<br>
        Comments: {{ $comment->comments }}<br>
        <br>
        @endif
        @endforeach
    </div>
</div>
@endforeach
@else
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Heads Be Rolling Soon</h2>
        </div>
        <div class="card-body">
            No fc's have submitted reports recently.
        </div>
    </div>
</div>
@endif
@endsection