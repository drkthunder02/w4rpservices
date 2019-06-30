@extends('layouts.b4')
@section('content')

@if($requests != null)
    <div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>Timestamp</th>
                <th>Pilot</th>
                <th>Fleet Commander</th>
                <th>zKillboard</th>
                <th>Total Loss Value</th>
                <th>Type of Ship</th>
                <th>Fleet Type</th>
                <th>Actual SRP</th>
                <th>Notes</th>
            </thead>
            <tbody>
                @foreach($requests as $row)
                    <tr>
                        {!! Form::open(['action' => 'SRP\SRPAdminController@processSRPRequest', 'method' => 'POST']) !!}
                        <td>{{ $row['created_at'] }}</td>
                        <td>{{ $row['character_name'] }}</td>
                        <td>{{ $row['fleet_commander_name'] }}</td>
                        <td><a href="{{ $row['zkillboard'] }}" target="_blank">zKill Link</a></td>
                        <td>{{ $row['loss_value'] }}</td>
                        <td>{{ $row['ship_type'] }}</td>
                        <td>{{ $row['fleet_type'] }}</td>
                        <td>
                            {{ $row['actual_srp'] }}
                            {{ Form::hidden('paid_value', $row['actual_srp'], ['class' => 'form-control']) }}
                        </td>
                        <td>{{ Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'notes', 'rows' => 2, 'cols' => 15, 'style' => 'resize:none']) }}
                        </tr>
                        <tr>
                        <div class="form-group">
                            <td>{{ Form::hidden('pay_out', $row['id'], ['class' => 'form-control']) }}</td>
                            <td>
                                {{ Form::label('approved', 'Approve') }}
                                {{ Form::radio('approved', 'Approved', false, ['class' => 'form-control']) }}
                            </td>
                            <td>
                                {{ Form::label('approved', 'Deny') }}
                                {{ Form::radio('approved', 'Denied', false, ['class' => 'form-control']) }}
                            </td>
                            <td>
                                {{ Form::submit('Pay Out', ['class' => 'btn btn-primary']) }}
                            </td>
                        </div>
                        {!! Form::close() !!}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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