@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Register A New Fleet</h2>
    {!! Form::open(['action' => 'FleetsController@registerFleet', 'method' => 'POST']) !!}
        <div class="form-group col-md-6">
            {{ Form::label('fleet', 'Fleet') }}
            {{ Form::text('fleet', '', ['class' => 'form-control', 'placeholder' => 'Fleet URL']) }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection