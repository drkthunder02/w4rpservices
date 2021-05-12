@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Invoice Details</h2><br>
            <h3>Id: {!! $invoice !!}</h3><br>
            <h3>Amount: {!! number_format($totalPrice, 2, ".", ",") !!} ISK</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Ore Name</th>
                    <th>Quantity</th>
                </thead>
                <tbody>
                @foreach($ores as $name => $quantity)
                <tr>
                    <td>{{ $name }}</td>
                    <td>{{ $quantity }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection