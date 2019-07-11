@extends('srp.layouts.b4')
@section('content')

<div class="container">
    <div id="chart-div"></div>
    {!! $lava->render('PieChart', 'SRP Stats', 'chart-div') !!}
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
    <div id="cost-codes-div"></div>
    {!! $lava->render('ColumnChart', 'Cost Codes', 'cost-codes-div') !!}
</div>
@endsection