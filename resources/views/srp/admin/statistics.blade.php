@extends('layouts.b4')
@section('content')

@barchart('ISK, 'fc_loss_div')
<div id="poll_div"></div>
{!! $lava->render('BarChart', 'ISK', 'poll_div') !!}

@endsection