@extends('srp.layouts.b4')
@section('content')

<div id="chart-div"></div>
{!! Lava::render('PieChart', 'IMDB', 'chart-div') !!}

@endsection