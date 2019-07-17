@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Ship Replacement Program Form</h2>
    <h3>Enter the details of your loss.</h3>
</div>
<div class="container col-md-6">
    {!! Form::open([
        'action' => 'SRP\SRPController@storeSRPFile',
        'method' => 'POST'
    ]) !!}
    <div class="form-group">
    {{ Form::label('character', 'Character') }}
    {{ Form::select('character', $characters, null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
    {{ Form::label('FC', 'Fleet Commander') }}
    {{ Form::text('FC', null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
    {{ Form::label('FleetType', 'Fleet Type') }}
    {{ Form::select('FleetType', $fleetTypes, 'None', ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
    {{ Form::label('zKillboard', 'zKillboard Link') }}
    {{ Form::text('zKillboard', null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
    {{ Form::label('LossValue', 'Loss Value') }}
    {{ Form::text('LossValue', null, ['class' => 'form-control', 'placeholder' => '1.00']) }}
    </div>
    <div class="form-group">
    {{ Form::label('ShipType', 'Type of Ship') }}
    {{ Form::select('ShipType', $shipTypes, 'None', ['class' => 'form-control']) }}
    </div>
{{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
</div>
@endsection