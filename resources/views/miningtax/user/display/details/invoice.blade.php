@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Invoice Details</h2><br>
            <h3>Id: {!! $invoice !!}</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Ore Name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </thead>
                <tbody>
                @foreach($ores as $ore)
                <tr>
                    <td>{{ $ore['ore_name'] }}</td>
                    <td>{{ $ore['quantity'] }}</td>
                    <td>{{ number_format($ore['amount'], 2, ".", ",") }} ISK </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection