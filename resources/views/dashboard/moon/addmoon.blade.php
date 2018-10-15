@extends('layouts.b4')

@section('content')
    <h2>Add A New Moon</h2>
    {!! Form::open(['action' => 'MoonsController@storeMoon', 'method' => 'POST']) !!}
        <div class="form-group">
            {{ Form::label('region', 'Region') }}
            {{ Form::text('region', '', ['class' => 'form-control', 'placeholder' => 'Region']) }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
@endsection