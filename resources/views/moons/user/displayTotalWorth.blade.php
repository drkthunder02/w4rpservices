@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <h2>Total Worth of the Moon / Month</h2>
    <div class="jumbotron">
        Total Moon Worth: {{ $totalWorth }} ISK<br>
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Moon Composition</h2>
        </div>
        <div class="card-body">
            <h4>Reprocessing Percentage set at: {{ $reprocessing }}</h4>
            <h4>Length of pull is 1 month</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Mineral</th>
                    <th>Quantity</th>
                </thead>
                <tbody>
                    @foreach($composition as $key => $value)
                        @if($value > 0)
                        <tr>
                            <td>{{ str_replace('_', ' ', $key) }}</td>
                            <td>{{ number_format($value, 0, ".", ",") }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
