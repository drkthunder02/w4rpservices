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
                    <th>Structure</th>
                    <th>Industry Taxes</th>
                </thead>
                <tbody>
                    @for($i = 0; $i < $months; $i++)
                        <tr>
                            {{ dd($totalTaxes) }}
                            <td>{{ $totalTaxes[$i]['MonthStart'] }}</td>
                            <td>{{ $totalTaxes[$i]['Structure'] }}</td>
                            <td>{{ $totalTaxes[$i]['IndustryTaxes'] }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection