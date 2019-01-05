@extends('layouts.b4')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            Structure Taxes
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <th>Month</th>
                    <th>Market Tax</th>
                    <th>Market Revenue Minus Fuel Cost</th>
                </thead>
                <tbody>
                    @for($i = 0; $i < 12; $i++)
                    <tr>
                        <td>{{ $totalTaxes[$i]['start'] }}</td>
                        <td>{{ $totalTaxes[$i]['MarketTax'] }}</td>
                        <td>{{ $totalTaxes[$i]['MarketRevenue'] }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection