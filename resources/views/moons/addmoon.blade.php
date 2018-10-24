@extends('layouts.b4')
@include('layouts.navbar')
@section('content')
<div class="container">
    <h2>Add A New Moon</h2>
    {!! Form::open(['action' => 'MoonsController@addMoon', 'method' => 'POST']) !!}
        <div class="form-group col-md-6">
            {{ Form::label('region', 'Region') }}
            {{ Form::text('region', '', ['class' => 'form-control', 'placeholder' => 'Region']) }}
            {{ Form::label('system', 'System') }}
            {{ Form::text('system', '', ['class' => 'form-control', 'placeholder' => 'System']) }}
            {{ Form::label('struture', 'Structure Name') }}
            {{ Form::text('structure', '', ['class' => 'form-control', 'placeholder' => 'Structure Name']) }}
            {{ Form::label('firstore', 'First Ore') }}
            {{ Form::text('firstore', '', ['class' => 'form-control', 'placeholder' => 'First Ore Name']) }}
            {{ Form::label('firstquan', 'First Ore Quantity') }}
            {{ Form::text('firstquan', '', ['class' => 'form-control', 'placeholder' => 'Second Ore Quantity']) }}
            {{ Form::label('secondore', 'Second Ore') }}
            {{ Form::text('secondore', '', ['class' => 'form-control', 'placeholder' => 'Second Ore Name']) }}
            {{ Form::label('secondquan', 'Second Ore Quantity') }}
            {{ Form::text('secondquan', '', ['class' => 'form-control', 'placeholder' => 'Second Ore Quantity']) }}
            {{ Form::label('thirdore', 'Third Ore') }}
            {{ Form::text('thirdore', '', ['class' => 'form-control', 'placeholder' => 'Third Ore Name']) }}
            {{ Form::label('thirdquan', 'Third Ore Quantity') }}
            {{ Form::text('thirdquan', '', ['class' => 'form-control', 'placeholder' => 'Third Ore Quantity']) }}
            {{ Form::label('fourthore', 'Fourth Ore') }}
            {{ Form::text('fourthore', '', ['class' => 'form-control', 'placeholder' => 'Fourth Ore Name']) }}
            {{ Form::label('fourthquan', 'Fourth Ore Quantity') }}
            {{ Form::text('fourthquan', '', ['class' => 'form-control', 'placeholder' => 'Fourth Ore Quantity']) }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection