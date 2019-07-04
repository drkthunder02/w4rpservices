@extends('srp.layouts.b4')
@section('content')

<div class="container">
    <div id="chart-div"></div>
    {!! $lava->render('PieChart', 'SRP Stats', 'pop_div') !!}
</div>

<div class="container">
    <div id="under-review-div"></div>
    {!! $lava->render('GaugeChart', 'SRP', 'under-review-div') !!}
</div>

<div class="container">
    <div id="fc-losses-div"></div>
    {!! $lava->render('BarChart', 'FCs', 'fc-losses-div') !!}
</div>

<div class="container">
    {{ $start }}<br>
    {{ $end }}<br>
</div>
@endsection