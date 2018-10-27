@extends('layouts.b4')
@include('layouts.navbar')
@section('content')
<div class="container">
    <h2>Register for Warped Intentions Wiki<h2>
        {!! Form::open(['action' => 'WikiController@storeRegister', 'method' => 'POST']) !!}
        <div class="form-group col-md-6">
            {{ Form::label('password', 'Password') }}
            {{ Form::password('password', 'password', ['class' => 'form-control']) }}
            {{ Form::label('password2', 'Repeat Password') }}
            {{ Form::password('password2', 'password', ['class' => 'form-control']) }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
        {!! Form::close() !!}
</div>
@endsection