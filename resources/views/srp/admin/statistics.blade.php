@extends('layouts.b4')
@section('content')

@barchart('FC Losses', 'fc_loss_div')
<div id="poll_div"></div>
{{ $lava->render('BarChart', 'FC Losses', 'poll_div') }}

@endsection