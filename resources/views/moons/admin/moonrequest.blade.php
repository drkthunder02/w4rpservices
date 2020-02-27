@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Alliance Moon Requests</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Region</th>
                    <th>System - Planet - Moon</th>
                    <th>Corporation</th>
                    <th>Requestor</th>
                    <th> </th>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    <tr>
                        {!! Form::open(['action' => 'Moons\MoonsAdminController@storeApprovedMoonRequest', 'method' => 'POST']) !!}
                        <td>{{ $req->region }}</td>
                        <td>{{ $req->system . " - " . $req->planet . " - " . $req->moon }}</td>
                        <td>{{ $req->corporation_name }}</td>
                        <td>{{ $req->requestor_name }}</td>
                        <td>
                            {{ Form::hidden('id', $req->id) }}
                            {{ Form::hidden('system', $req->system) }}
                            {{ Form::hidden('planet', $req->planet) }}
                            {{ Form::hidden('moon', $req->moon) }}
                            {{ Form::select('status', [
                                'Approved' => 'Approved',
                                'Denied' => 'Denied',
                            ], 'Select') }}
                            {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
                        </td>
                        
                        {!! Form::close() !!}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection