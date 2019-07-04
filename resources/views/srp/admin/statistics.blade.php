@extends('srp.layouts.b4')
@section('content')

<div class="container">
    <div id="chart-div"></div>
    {!! Lava::render('PieChart', 'SRP Stats', 'chart-div') !!}
</div>

<div class="container">
    <div id="under-review-div"></div>
    {!! Lava::render('GaugeChart', 'SRP', 'under-review-div') !!}
</div>

<div class="container">
    <div id="fc-losses-div"></div>
    {!! Lava::render('BarChart', 'FCs', 'fc-losses-div') !!}
</div>
@endsection