@extends('srp.layouts.b4')
@section('content')

<div class="container">
    <div id="chart-div"></div>
    {!! $lava->render('PieChart', 'SRP Stats', 'chart-div') !!}
</div>

<div class="container">
    <div id="under-review-div"></div>
    {!! $guage->render('GaugeChart', 'SRP', 'under-review-div') !!}
</div>
@endsection