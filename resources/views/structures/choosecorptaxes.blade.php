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
                    <th>Refinery Tax</th>
                    <th>Refinery Revenue Minus Fuel Cost</th>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $totalTaxes['thisMonthStart'] }}</td>
                        <td>{{ $totalTaxes['thisMonthMarket'] }}</td>
                        <td>{{ $totalTaxes['thisMonthRevMarket'] }}</td>
                        <td>{{ $totalTaxes['thisMonthRefinery'] }}</td>
                        <td>{{ $totalTaxes['thisMonthRevRefinery'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ $totalTaxes['lastMonthStart'] }}</td>
                        <td>{{ $totalTaxes['lastMonthMarket'] }}</td>
                        <td>{{ $totalTaxes['lastMonthRevMarket'] }}</td>
                        <td>{{ $totalTaxes['lastMonthRefinery'] }}</td>
                        <td>{{ $totalTaxes['lastMonthRevRefinery'] }}</td>
                    </tr>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection