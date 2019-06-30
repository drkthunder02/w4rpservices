@extends('layouts.b4')
@section('content')
{!! Form::open(['action' => 'SRP\SRPController@processForm', 'method' => 'POST']) !!}
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
                @foreach($srp as $row)
                    <tr>
                        <td>{{ $row['created_at'] }}</td>
                        <td>{{ $row['pilot'] }}</td>
                        <td>{{ $row['fc'] }}</td>
                        <td>{{ $row['link'] }}</td>
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
    </div>
{{ Form::submit('Pay Out', ['class' => 'form-control']) }}
{!! Form::close() !!}
@endsection