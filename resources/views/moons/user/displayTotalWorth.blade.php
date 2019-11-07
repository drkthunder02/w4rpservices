@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Total Worth of the Moon / Month</h2>
    <div class="jumbotron">
        Total Moon Goo + Ore Worth: {{ $totalWorth }} ISK<br>
        Total Moon Goo Worth: {{ $totalGoo }} ISK<br>
    </div>
</div>
<br>
<div class="container">
    <h2>Moon Composition</h2>
    <div class="jumbotron">
        <table class="table table-striped table-bordered">
            <thead>
                <th>Mineral</th>
                <th>Quantity</th>
            </thead>
            <tbody>
                @foreach($composition as $key => $value)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
