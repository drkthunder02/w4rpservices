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
                        <td>{{ $totalTaxes['thisMonthReprocessing'] }}</td>
                        <td>{{ $totalTaxes['thisMonthMarket'] }}
                    </tr>
                    <tr>
                        <td>Last Month</td>
                        <td>{{ $totalTaxes['lastMonthReprocessing'] }}</td>
                        <td>{{ $totalTaxes['lastMonthMarket'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection