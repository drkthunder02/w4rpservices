@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Moons in W4RP Space</h2>
    <ul class="nav nav-pills">
        <li class="active"><a data-toggle="pill" href="#6X7-JO">6X7-JO</a></li>
        <li><a data-toggle="pill" href="#A-803L">A-803L</a></li>
    </ul>

    <div class="tab-content">
        <div id="6X7-JO" class="tab-pane fade in active">
            <h3>Table Goes here</h3>
        </div>
        <div id="A-803L" class="tab-pane fade">
            <h3>Table Goes Here</h3>
        </div>
    </div>
</div>
@endsection