@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Rental Form for Moon Rental</h2><br>
            Moon: {{ $moon->moon_name }}<br>
            @foreach($ores as $ore)
            {{ $ore->ore_name }} : {{ number_format(($ore->ore_quantity * 100.00), 2, ".", ",") }}<br>
            @endforeach
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'MiningTaxes\MiningTaxesController@storeMoonRentalForm']) !!}
            {{ Form::hideen('moon_id', $moon->moon_id) }}
            {{ Form::hidden('moon_name', $moon->moon_name) }}
            <div class="form-group">
                {{ Form::label('rental_start', 'Day of Rental Start') }}
                {{ Form::date('rental_start', Carbon\Carbon::now(), ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('rental_end', 'Day of Rental End') }}
                {{ Form::date('rental_end', Carbon\Carbon::now()->addMonths(3), ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('entity_type', 'Select Character or Corporation') }}
                {{ Form::select('entity_type', ['Character', 'Corporation'], ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('entity_name', 'Enter Name for Rental') }}
                {{ Form::text('entity_name', '', ['class' => 'form-control', 'placeholder' => 'Enter your character name or corporation name to follow the previous selection.']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-warning']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection