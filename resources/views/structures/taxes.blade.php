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
                    <th>Market Revenue</th>
                    <th>Refinery Tax</th>
                    <th>Refinery Revenue</th>
                </thead>
                <tbody>
                    <tr>
                        <td>This Month</td>
                        <td>{{ $totalTaxes['thisMonthMarket'] }}</td>
                        <td>{{ $totalTaxes['thisMonthRevMarket'] }}</td>
                        <td>{{ $totalTaxes['thisMonthRefinery'] }}</td>
                        <td>{{ $totalTaxes['thisMonthRevRefinery'] }}</td>
                    </tr>
                    <tr>
                        <td>Last Month</td>
                        <td>{{ $totalTaxes['lastMonthMarket'] }}</td>
                        <td>{{ $totalTaxes['lastMoMarketGeneration'] }}</td>
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