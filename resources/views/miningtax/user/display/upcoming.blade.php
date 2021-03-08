@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Mining Calendar</h2>
        </div>
        <div class="card-body">
            <div id="extractions_div"></div>
            {!! $lava->render('CalendarChart', 'Extractions', 'extractions_div') !!}
        </div>
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Upcoming Extractions</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Structure Name</th>
                    <th>Start Time</th>
                    <th>Arrival Time</th>
                    <th>Decay Time</th>
                </thead>
                <tbody>
                @foreach($structures as $ex)
                <tr>
                    <td>{!! $ex['structure_name'] !!}</td>
                    <td>{!! $ex['start_time'] !!} UTC</td>
                    <td>{!! $ex['arrival_time'] !!} UTC</td>
                    <td>{!! $ex['decay_time'] !!} UTC</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection