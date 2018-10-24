@extends('layouts.b4')
@include('layouts.navbar')
@section('content')
<div class="container">
    <div class="jumbotron">
        <table class="table table-striped">
            <thead>
                <th>Region</th>
                <th>System</th>
                <th>Name</th>
                <th>First Ore</th>
                <th>Quantity</th>
                <th>Second Ore</th>
                <th>Quantity</th>
                <th>Third Ore</th>
                <th>Quantity</th>
                <th>Fourth Ore</th>
                <th>Quantity</th>
                <th>Rental Price</th>
                <th>Renter</th>
                <th>Rental End</th>
            </thead>
            <tbody>
                @foreach ($moons as $moon)
                    <tr>
                        <td>{{ $moon->region }}</td>
                        <td>{{ $moon->system }}</td>
                        <td>{{ $moon->structure }}</td>
                        <td>{{ $moon->firstore }}</td>
                        <td>{{ $moon->firstquan }}</td>
                        <td>{{ $moon->secondore }}</td>
                        <td>{{ $moon->secondquan }}</td>
                        <td>{{ $moon->thirdore }}</td>
                        <td>{{ $moon->thirdquan }}</td>
                        <td>{{ $moon->fourthore }}</td>
                        <td>{{ $moon->price }}</td>
                        <td>{{ $moon->renter }}</td>
                        <td>{{ $moon->rentend }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection