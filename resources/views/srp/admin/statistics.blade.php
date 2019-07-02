@extends('layouts.b4')
@section('content')

@barchart('FC Losses', 'fc_loss_div')
<div id="poll_div"></div>
{{ Lava::render('BarChart', 'Food Poll', 'poll_div') }}

@endsection