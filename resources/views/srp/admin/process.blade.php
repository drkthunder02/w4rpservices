@extends('layouts.b4')
@section('content')

@if($requests != null)
    <div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>Timestamp</th>
                <th>Pilot</th>
                <th>FC</th>
                <th>zKillboard</th>
                <th>Total Loss</th>
                <th>Ship Type</th>
                <th>Fleet Type</th>
                <th>Actual SRP</th>
                <th>Notes</th>
                <th>Approve</th>
                <th>Deny</th>
                <th>Process</th>
            </thead>
            <tbody>
                @foreach($requests as $row)
                    <tr>
                        {!! Form::open(['action' => 'SRP\SRPAdminController@processSRPRequest', 'method' => 'POST']) !!}
                        <td>
                            {{ Form::hidden('id', $row['id'], ['class' => 'form-control']) }}
                            {{ $row['created_at'] }}
                        </td>
                        <td>
                            {{ $row['character_name'] }}
                        </td>
                        <td>
                            {{ $row['fleet_commander_name'] }}
                        </td>
                        <td>
                            <a href="{{ $row['zkillboard'] }}" target="_blank">zKill Link</a>
                        </td>
                        <td>
                            {{ number_format($row['loss_value'], 2, '.', ',') }}
                        </td>
                        <td>
                            {{ $row['ship_type'] }}
                        </td>
                        <td>
                            {{ $row['fleet_type'] }}
                        </td>
                        <td>
                            {{ number_format($row['actual_srp'], 2, ".", ",") }}
                            {{ Form::hidden('paid_value', $row['actual_srp'], ['class' => 'form-control']) }}
                        </td>
                        <td>
                            {{ Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'notes', 'rows' => 2, 'cols' => 15, 'style' => 'resize:none']) }}
                        </td>
                        <td>
                            {{ Form::radio('approved', 'Approved', false, ['class' => 'form-control']) }}
                        </td>
                        <td>
                            {{ Form::radio('approved', 'Denied', false, ['class' => 'form-control']) }}
                        </td>
                        <td>
                            {{ Form::submit('Process', ['class' => 'btn btn-primary']) }}
                        </td>
                        </tr>
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