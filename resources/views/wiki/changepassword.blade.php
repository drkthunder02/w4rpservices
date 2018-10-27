@extends('layouts.b4')
@include('layouts.navbar')
@section('content')
<div class="container">
    <h2>Change Wiki Password</h2>
    {!! Form::open(['action' => 'WikiController@changePassword', 'method' => 'POST']) !!}
    <div class="form-group col-md-6">
        {{ Form::label('password', 'Password') }}
        {{ Form::password('password', '',['class' => 'form-control']) }}
        {{ Form::label('password2', 'Repeat Password') }}
        {{ Form::password('password2', '', ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection