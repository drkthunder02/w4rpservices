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
                    @foreach($totalTaxes as $tax)
                        <tr>
                            <td>{{ $tax['start'] }}</td>
                            <td>{{ $tax['tax'] }}</td>
                            <td>{{ $tax['revenue'] }}<td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection