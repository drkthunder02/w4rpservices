@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Change Wiki Password</h2>
                </div>
                <div class="card-body">
                    <h3>Your username is: {{ $name }} </h3><br>
                    {!! Form::open(['action' => 'WikiController@changePassword', 'method' => 'POST']) !!}
                    <div class="form-group col-md-6">
                        {{ Form::label('password', 'Password') }}
                        {{ Form::password('password', ['class' => 'form-control']) }}
                        {{ Form::label('password2', 'Repeat Password') }}
                        {{ Form::password('password2', ['class' => 'form-control']) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection