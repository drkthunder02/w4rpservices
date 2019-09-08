@extends('layouts.b4')
@section('content')
<br>
<div class="card">
    <div class="card-header">
        <h2>Structure Requests</h2>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <th>Corporation</th>
                <th>System</th>
                <th>Structure Size</th>
                <th>Structure Type</th>
                <th>Drop Time</th>
                <th>Requester</th>
                <th>Delete</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($reqs as $req)
                    <tr>
                        <td>{{ $req->corporation_name }}</td>
                        <td>{{ $req->system }}</td>
                        <td>{{ $req->structure_size }}</td>
                        <td>{{ $req->structure_type }}</td>
                        <td>{{ $req->requested_drop_time }}</td>
                        <td>{{ $req->requester }}</td>
                        {!! Form::open(['action' => 'Logistics\StructureRequestController@deleteRequest', 'method' => 'POST']) !!}
                        <td>
                            {{ Form::hidden('id', $req->id, ['class' => 'form-control']) }}
                            {{ Form::radio('delete', 'Delete', false, ['class' => 'form-conotrol']) }}
                        </td>
                        <td>
                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                        </td>
                        {!! Form::close() !!}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection