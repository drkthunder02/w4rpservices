@extends('layouts.user.dashb4')
@section('content')
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
                @foreach($extractions as $ex)
                <tr>
                    <td>{{ $ex['structure_name'] }}</td>
                    <td>{{ $ex['start_time'] }}</td>
                    <td>{{ $ex['arrival_time'] }}</td>
                    <td>{{ $ex['decay_time'] }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection