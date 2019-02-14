@extends('layouts.b4')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            Structure Industry Taxes
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <th>Month</th>
                    <th>Reprocessing Taxes Minus Fuel Cost / Month</th>
                    <th>Reprocessing Revenue</th>
                </thead>
                <tbody>
                    @foreach($taxes as $tax)
                        <tr>
                            <td>{{ $tax['date'] }}</td>
                            <td>{{ $tax['tax'] }}</td>
                            <td>{{ $tax['revenue'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection