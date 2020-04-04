@extends('layouts.b4')
@section('content')
<div class="container col-md-12">
    <div class="card">
        <div class="card-header">
            <h2>Approved SRP Requests</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Timestamp</th>
                    <th>Pilot</th>
                    <th>FC</th>
                    <th>ZKillboard</th>
                    <th>Total Loss</th>
                    <th>Paid Out</th>
                    <th>Ship Type</th>
                    <th>Fleet Type</th>
                    <th>Notes</th>
                </thead>
                <tbody>
                    @foreach($srpApproved as $approved)
                        <tr>
                            <td>{{ $approved->created_at }}</td>
                            <td>{{ $approved->character_name }}</td>
                            <td>{{ $approved->fleet_commander_name }}</td>
                            <td><a href="{{ $approved->zkillboard }}" target="_blank">zKill Link</a></td>
                            <td>{{ number_format($approved->loss_value, 2, ".", ",") }}</td>
                            <td>{{ number_format($approved->paid_value, 2, ".", ",") }}</td>
                            <td>{{ $approved->ship_type }}</td>
                            <td>{{ $approved->fleet_type }}</td>
                            <td>{{ $approved->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $srpApproved->links() }}
        </div>
    </div>
</div>
<br>
<div class="container col-md-12">
    <div class="card">
        <div class="card-header">
            <h2>Denied SRP Requests</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Timestamp</th>
                    <th>Pilot</th>
                    <th>FC</th>
                    <th>zKill</th>
                    <th>Total Loss</th>
                    <th>Ship Type</th>
                    <th>Fleet Type</th>
                    <th>Notes</th>
                </thead>
                <tbody>
                    @foreach($srpDenied as $denied)
                        <tr>
                            <td>{{ $denied->created_at }}</td>
                            <td>{{ $denied->character_name }}</td>
                            <td>{{ $denied->fleet_commander_name }}</td>
                            <td><a href="{{ $denied->zkillboard }}" target="_blank">zKill Link</a></td>
                            <td>{{ number_format($denied->loss_value, 2, ".", ",") }}</td>
                            <td>{{ $denied->ship_type }}</td>
                            <td>{{ $denied->fleet_type }}</td>
                            <td>{{ $denied->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $srpDenied->links() }}
        </div>
    </div>
</div>
@endsection