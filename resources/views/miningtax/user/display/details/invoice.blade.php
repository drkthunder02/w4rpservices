@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Invoice Details</h2>
            <h3>Id: {!! $invoice->invoice_id !!}</h3>
            <h3>Amount: {!! number_format($totalPrice, 2, ".", ",") !!} ISK</h3>
            <h3>Invoice Date: {!! $invoice->date_issued !!}</h3>
            <h3>Invoice Due Date: {!! $invoice->date_due !!}</h3>
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
    <br>
    <div class="card">
        <div class="card-header">
            <h2>Ledger Details</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Character Name</th>
                    <th>Observer Name</th>
                    <th>Ore Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Tax Amount</th>
                </thead>
                <tbody>
                @foreach($moons as $moon)
                <tr>
                    <td>{{ $moon['character_name'] }}</td>
                    <td>{{ $moon['observer_name'] }}</td>
                    <td>{{ $moon['ore_name'] }}</td>
                    <td>{{ $moon['quantity'] }}</td>
                    <td>{{ number_format($moon['amount'], 2, ".", ",") }}</td>
                    <td>{{ $moon['tax_amount'] }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection