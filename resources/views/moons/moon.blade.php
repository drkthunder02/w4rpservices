@extends('layouts.b4')
@include('layouts.navbar')
@section('content')
<div class="container">
    <div class="jumbotron">
        <table class="table table-striped">
            <thead>
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
                {{ $html }}
            </tbody>
        </table>
    </div>
</div>
@endsection