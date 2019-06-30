@extends('layouts.b4')
@section('content')
{!! Form::open(['action' => 'SRP\SRPAdminController@processSRPRequest', 'method' => 'POST']) !!}
    <div class="container col-md-12">
        @if($requests != null)
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
                        <td>{{ $row['pilot'] }}</td>
                        <td>{{ $row['fc'] }}</td>
                        <td><a href="{{ $row['link'] }}">zKill Link</a></td>
                        <td>{{ $row['loss'] }}</td>
                        <td>{{ $row['ship_type'] }}</td>
                        <td>{{ $row['fleet_type'] }}</td>
                        <td>{{ $row['actual_srp'] }}</td>
                        <td>{{ Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'notes', 'rows' => 4, 'cols' => 30, 'style' => 'resize:none']) }}
                        <td>{{ Form::radio('pay_out', $row['id'], false, ['class' => 'form-control']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <h3>No Open SRP Requests</h3>            
        @endif
            
    </div>
{{ Form::submit('Pay Out', ['class' => 'form-control']) }}
{!! Form::close() !!}
@endsection