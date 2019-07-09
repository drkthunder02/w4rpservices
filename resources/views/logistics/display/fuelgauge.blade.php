@extends('logistics.layouts.b4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>{{ $name }}</h2>
        </div>
        <div class="card-body">
            <div id="fuel-gauge-div"></div>
            {!! $lava->render('GaugeChart', 'Liquid Ozone', 'fuel-gauge-div') !!}
        </div>
    </div>
</div>
<br>
@endsection