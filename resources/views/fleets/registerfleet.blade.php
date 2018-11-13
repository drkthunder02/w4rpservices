@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Register A New Fleet</h2>
    {!! Form::open(['action' => 'FleetsController@registerFleet', 'method' => 'POST']) !!}
        <div class="form-group col-md-6">
            {{ Form::label('fleetUri', 'Fleet') }}
            {{ Form::text('fleetUri', '', ['class' => 'form-control', 'placeholder' => 'Fleet URL']) }}
            {{ Form::label('description', 'Fleet Name') }}
            {{ Form::text('description', '', ['class' => 'form control', 'placeholder' => 'Fleet Name']) }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection