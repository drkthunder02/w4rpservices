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
                <th>FC</th>
                <th>Requester</th>
                <th>Completed</th>
                <th>Delete</th>
            </thead>
            <tbody>
                @foreach($reqs as $req)
                    <tr>
                        <td>{{ $req->corporation_name }}</td>
                        <td>{{ $req->system }}</td>
                        <td>{{ $req->structure_size }}</td>
                        <td>{{ $req->structure_type }}</td>
                        <td>{{ $req->requested_drop_time }}</td>
                        <td>{{ $req->assigned_fc }}</td>
                        <td>{{ $req->requester }}</td>
                        <td>{{ $req->completed }}</td>
                        <!--  Create Form -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection