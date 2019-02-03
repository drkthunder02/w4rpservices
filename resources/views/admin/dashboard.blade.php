@extends('layouts.b4')
@section('content')
<div class="container">
    <br>
    <h2>Admin Dashboard</h2>
    <br>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-6 card">
            <div class="card-header">
                Add Permission for User
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'AdminController@addPermission', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('user', 'User') }}
                    {{ Form::select('user', $data['users'], null, ['class' => 'form-control']) }}
                    {{ Form::select('permission', $data['permissions'], null, ['class' => 'form-control']) }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-6 card">
            <div class="card-header">
                Remove User
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'AdminController@removeUser', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('user', 'User') }}
                    {{ Form::select('user', $data['users'], null, ['class' => 'form-control']) }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection