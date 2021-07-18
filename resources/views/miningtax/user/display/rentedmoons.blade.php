@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Moons Currently Rented</h2>
        </div>
        <div class="card-body">
            @if(sizeof($moons) == 0)
            <h2>No moons currently rented by you or your corporation.</h2>
            @else
            <table class="table table-striped table-bordered">
                <thead>
                    <th>System</th>
                    <th>Moon Name</th>
                    <th>Worth</th>
                    <th>Rental</th>
                    <th>Ores</th>
                </thead>
                <tbody>
                @foreach($moons as $moon)
                    <tr>
                        <td>{{ $moon['system'] }}</td>
                        <td>{{ $moon['moon_name'] }}</td>
                        <td>{{ number_format($moon['worth_amount'], 2, ".", ",") }}</td>
                        <td>{{ number_format($moon['rental_amount'], 2, ".", ",") }}</td>
                        <td>
                        @foreach($moon['ores'] as $ore)
                        {{ $ore['ore_name'] }} : {{ number_format($ore['quantity'], 0, ".", ",") }}<br>
                        @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection