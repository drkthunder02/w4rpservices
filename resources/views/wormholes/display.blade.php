@extends('layouts.b4')
@section('content')
<div class="container">
    <table class="table table-striped table-bordered">
        <thead>
            <th>System</th>
            <th>Sig ID</th>
            <th>Duration Left</th>
            <th>Scan Time</th>
            <th>WH Class</th>
            <th>Hole Size</th>
            <th>Stability</th>
            <th>Mass Allowed</th>
            <th>Individual Mass</th>
            <th>Regeneration</th>
            <th>Max Stable Time</th>
            <th>Details</th>
            <th>Link</th>
        </thead>
        <tbody>
            @foreach($wormholes as $wormhole)
                <tr>
                    <td>{{ $wormhole->system }}</td>
                    <td>{{ $wormhole->sig_id }}</td>
                    <td>{{ $wormhole->duration_left }}</td>
                    <td>{{ $wormhole->dateTime }}</td>
                    <td>{{ $wormhole->class }}</td>
                    <td>{{ $wormhole->hole_size }}</td>
                    <td>{{ $wormhole->stability }}</td>
                    <td>{{ $wormhole->mass_allowed }}</td>
                    <td>{{ $wormhole->individual_mass }}</td>
                    <td>{{ $wormhole->regeneration }}</td>
                    <td>{{ $wormhole->max_stable_time }}</td>
                    <td>{{ $wormhole->details }}</td>
                    <td>{{ $wormhole->link }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection