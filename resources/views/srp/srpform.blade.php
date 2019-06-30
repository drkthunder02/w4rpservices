@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Ship Replacement Program Form</h2>
    <h3>Enter the details of your loss.</h3>
    {!! Form::open([
        'action' => 'SRP\SRPController@storeSRPFile',
        'method' => 'POST'
    ]) !!}
    {{ Form::label('FC', 'Fleet Commander') }}
    {{ Form::text('FC', null, ['class' => 'form-control']) }}
    {{ Form::label('FleetType', 'Fleet Type') }}
    {{ Form::select('FleetType', [
        'Home Defense' => 'Home Defense',
        'Legacy Ops' => 'Legacy Ops',
        'Strat Op' => 'Strat Op',
        'CTA' => 'CTA',
        ], 'None', ['class' => 'form-control']) }}
    {{ Form::label('zKillboard', 'zKillboard Link') }}
    {{ Form::text('zKillboard', null, ['class' => 'form-control']) }}
    {{ Form::label('LossValue', 'Loss Value') }}
    {{ Form::text('LossValue', null, ['class' => 'form-control', 'placeholder' => '1.00']) }}
    {{ Form::label('ShipType', 'Type of Ship') }}
    {{ Form::select('ShipType', [
        'T1FDC' => 'T1 Frig / Dessie / Cruiser',
        'T1BC' => 'T1 Battlecruiser',
        'T2F' => 'T2 Frigate',
        'T3D' => 'T3 Destroyer',
        'T1T2Logi' => 'T1 & T2 Logisticis',
        'RI' => 'Recons / Interdictors',
        'T2C' => 'T2 Cruiser',
        'T3C' => 'T3 Cruiser',
        'COM' => 'Command Ship',
    ], 'None', ['class' => 'form-control']) }}
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
</div>
@endsection