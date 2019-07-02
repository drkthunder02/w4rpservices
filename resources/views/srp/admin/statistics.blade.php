@extends('srp.layouts.b4')
@section('content')

<div id="chart-div"></div>
{!! $lava->render('PieChart', 'IMDB', 'chart-div') !!}

@endsection