@extends('layouts.b4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Moon Ledger</h2><br>
            <p align="left">Shows mining from the last 30 days.</p>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Character</th>
                    <th>Corp Ticker</th>
                    <th>Ore Name</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </thead>
                <tbody>
                    @foreach($mining as $min)
                        <tr>
                            <td>{{ $min['character'] }}</td>
                            <td>{{ $min['corpTicker'] }}</td>
                            <td>{{ $min['ore'] }}</td>
                            <td>{{ $min['quantity'] }}</td>
                            <td>{{ $min['updated'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection