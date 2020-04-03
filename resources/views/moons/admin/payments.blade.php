@extends('layouts.admin.b4')
@section('content')

<div class="container">
    <!--  Put form header here for the form in the table -->
    <table class="table table-striped">
        <thead>
            <th>System</th>
            <th>Planet</th>
            <th>Moon</th>
            <th>Rental Corp</th>
            <th>Rental End</th>
            <th>Price</th>
            <th>Paid?</th>
        </thead>
        <tbody>
            @foreach($rentals as $rental)
            <tr>
                <td>{{ $rental['System'] }}</td>
                <td>{{ $rental['Planet'] }}</td>
                <td>{{ $rental['Moon'] }}</td>
                <td>{{ $rental['RentalCorp'] }}</td>
                <td>{{ $rental['RentalEnd'] }}</td>
                <td>{{ $rental['Price'] }}</td>
                <!-- spot for paid button -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection