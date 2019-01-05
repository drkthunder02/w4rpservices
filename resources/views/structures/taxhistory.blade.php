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
                    <th>Market Tax Minus Fuel Cost / Month</th>
                    <th>Market Revenue</th>
                </thead>
                <tbody>
                    @for($i = 0; $i < $months; $i++)
                    <tr>
                        <td>{{ $totalTaxes[$i]['MonthStart'] }}</td>
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