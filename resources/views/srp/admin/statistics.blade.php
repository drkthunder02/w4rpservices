@extends('srp.layouts.b4')
@section('content')

<div id="chart-div"></div>
{!! $lava->render('PieChart', 'SRP Stats', 'chart-div') !!}

@endsection