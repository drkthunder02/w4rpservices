@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Update Existing Moon</h2>
    {!! Form::open(['action' => 'MoonsController@storeUpdateMoon', 'method' => 'POST']) !!}
    <div class="form-group col-md-6">
        {{ Form::label('system', 'System') }}
        {{ Form::text('system', '', ['class' => 'form-control', 'placeholder' => 'System']) }}
        {{ Form::label('planet', 'Planet') }}
        {{ Form::text('planet', '', ['class' => 'form-control', 'placeholder' => 'Planet']) }}
        {{ Form::label('moon', 'Moon') }}
        {{ Form::text('moon', '', ['class' => 'form-control', 'placeholder' => 'Planet']) }}
        {{ Form::label('renter', 'Renter') }}
        {{ Form::text('renter', '', ['class' => 'form-control', 'placeholder' => 'Renter']) }}
        {{ Form::label('date', 'Rental End Date') }}
        {{ Form::text('date', '', ['class' => 'form-control', 'placeholder' => '01/01/1970'] )}}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection