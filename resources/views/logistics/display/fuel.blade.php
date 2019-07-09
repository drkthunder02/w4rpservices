@extends('layouts.b4')
@section('content')
@foreach($gates as $gate)
<div id="{{ $gate->div }}"></div>
{!! $lava->render('Gauge Chart', $gate->row, $gate->div) !!}
<br>
@endforeach
@endsection