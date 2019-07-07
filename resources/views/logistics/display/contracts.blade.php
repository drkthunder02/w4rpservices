@extends('layouts.b4')
@section('content')
@if($totalCount == 0)
<div class="card">
    <div class="card-header">
        <h2>Logistics Contracts</h2>
    </div>
    <div class="card-body">
        <h3>No open logistics contracts</h3>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <h2>Status</h2>
    </div>
    <div class="card-body">
        <div id="gauge-status-div"></div>
        {!! $lava->render('GaugeChart', 'Open Contracts', 'gauge-status-div') !!}
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h2>Open Contracts</h2>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <th>Date Issued</th>
                <th>Days To Complete</th>
                <th>Collateral</th>
                <th>Reward</th>
                <th>Volume</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach($open as $op)
                <tr>
                    <td>{{ $op->date_issued }}</td>
                    <td>{{ $op->days_to_complete }}</td>
                    <td>{{ $op->collateral }}</td>
                    <td>{{ $op->reward }}</td>
                    <td>{{ $op->volume }}</td>
                    <td>{{ $op->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h2>In Progress Contracts</h2>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <th>Date Issued</th>
                <th>Days To Complete</th>
                <th>Collateral</th>
                <th>Reward</th>
                <th>Volume</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach($inProgress as $in)
                <tr>
                    <td>{{ $in->date_issued }}</td>
                    <td>{{ $in->days_to_complete }}</td>
                    <td>{{ $in->collateral }}</td>
                    <td>{{ $in->reward }}</td>
                    <td>{{ $in->volume }}</td>
                    <td>{{ $in->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h2>Finished Contracts</h2>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <th>Date Issued</th>
                <th>Days To Complete</th>
                <th>Collateral</th>
                <th>Reward</th>
                <th>Volume</th>
                <th>Status</th>
            </thead>
            <tbody>
                @foreach($finished as $fin)
                <tr>
                    <td>{{ $fin->date_issued }}</td>
                    <td>{{ $fin->days_to_complete }}</td>
                    <td>{{ $fin->collateral }}</td>
                    <td>{{ $fin->reward }}</td>
                    <td>{{ $fin->volume }}</td>
                    <td>{{ $fin->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection