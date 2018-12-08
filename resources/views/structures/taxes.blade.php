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
                    <th>Reprocessing Tax</th>
                    <th>Market Tax</th>
                </thead>
                <tbody>
                    <tr>
                        <td>This Month</td>
                        <td>{{ $totalTaxes['thisMonthMarket'] }}
                    </tr>
                    <tr>
                        <td>Last Month</td>
                        <td>{{ $totalTaxes['lastMonthMarket'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            Structure Taxes without Fuel Cost Reduction
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <th>Month</th>
                    <th>Reprocessing Generation</th>
                    <th>Market Generation</th>
                </thead>
                <tbody>
                    <tr>
                        <td>This Month So Far</td>
                        <td>{{ $totalTaxes['thisMoMarketGeneration'] }}</td>
                    </tr>
                    <tr>
                        <td>Last Month</td>
                        <td>{{ $totalTaxes['lastMoMarketGeneration'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection