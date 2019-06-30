@extends('layouts.b4')
@section('content')
{!! Form::open(['action' => 'SRP\SRPAdminController@processSRPRequest', 'method' => 'POST']) !!}
@if($requests != null)
    <div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>Timestamp</th>
                <th>Pilot</th>
                <th>Fleet Commander</th>
                <th>zKillboard Link</th>
                <th>Total Loss Value</th>
                <th>Type of Ship</th>
                <th>Fleet Type</th>
                <th>Actual SRP</th>
                <th>Notes</th>
                <th>Pay Out</th>
            </thead>
            <tbody>
                @foreach($requests as $row)
                    <tr>
                        <td>{{ $row['created_at'] }}</td>
                        <td>{{ $row['character_name'] }}</td>
                        <td>{{ $row['fleet_commander_name'] }}</td>
                        <td><a href="{{ $row['zkillboard'] }}" target="_blank">zKill Link</a></td>
                        <td>{{ $row['loss_value'] }}</td>
                        <td>{{ $row['ship_type'] }}</td>
                        <td>{{ $row['fleet_type'] }}</td>
                        <td>{{ $row['actual_srp'] }}</td>
                        <td>{{ Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'notes', 'rows' => 4, 'cols' => 30, 'style' => 'resize:none']) }}
                        <td>{{ Form::radio('pay_out', $row['id'], false, ['class' => 'form-control']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ Form::submit('Pay Out', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
@else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>SRP Requests Dashboard</h2>
                </div>
                <div class="card-body">
                    <h3>No Open SRP Requests</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
        
@endsection