@extends('layouts.b4')
@include('layouts.navbar')
@section('content')
<div class="container">
    <h2>Update Existing Moon</h2>
    {!! Form::open(['action' => 'MoonsController@storeUpdateMoon', 'method' => 'POST']) !!}
    <div class="form-group col-md-6">
        {{ Form::label('name', 'Structure Name') }}
        {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Name']) }}
        {{ Form::label('renter', 'Renter') }}
        {{ Form::text('renter', '', ['class' => 'form-control', 'placeholder' => 'Renter']) }}
        {{ Form::label('date', 'Rental End Date') }}
        {{ Form::text('date', '', ['class' => 'form-control', 'placeholder' => '01/01/1970'] )}}
    </div>
    {{ Form:;submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection