@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>{{ $name }}</h2>
        </div>
        <div class="card-body">
            <div id="fuel-gauge-div"></div>
            {!! $lava->render('Gauage Chart', 'Fuel', 'fuel-gauge-div') !!}
        </div>
    </div>
</div>
@endsection