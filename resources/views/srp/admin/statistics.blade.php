@extends('layouts.b4')
@section('content')

<div id="approved_denied"></div>
{!! $adLava->render('PieChart', 'Approved / Denied', 'approved_denied') !!}

<div id="FC_Losses"></div>
{!! $lava->render('BarChart', 'ISK', 'FC_Losses') !!}

@endsection