@extends('layouts.b4')
@section('content')
<br>
<div class="row justify-content-center">
    <div class="container">
        <div id="fuel-div"></div>
        {!! $lava->render('GaugeChart', 'Liquid Ozone', 'fuel-div') !!}
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Jump Gate Fuel Status</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <th>Structure Name</th>
                    <th>System</th>
                    <th>Fuel Expires</th>
                    <th>Liquid Ozone Quantity</th>
                    <th>Fuel Gauge</th>
                </thead>
                <tbody>
                    @foreach($jumpGates as $jumpGate)
                        <tr>
                            <td>{{ $jumpGate['name'] }}</td>
                            <td>{{ $jumpGate['system'] }}</td>
                            <td>{{ $jumpGate{'fuel_expires'} }}</td>
                            <td>{{ $jumpGate['liquid_ozone'] }}</td>
                            <td><a href="{{ $jumpGate['link'] }}">Link</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection