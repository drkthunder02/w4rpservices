@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-6 card">
            <div class="card-header">
                Add Role for User
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'AdminController@addRole', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('user', 'User') }}
                    {{ Form::text('user', '', ['class' => 'form-control', 'placeholder' => 'Character Name']) }}
                    {{ Form::select('role', [
                        'None' => 'None',
                        'Guest' => 'Guest',
                        'User' => 'User',
                        'Admin' => 'Admin',
                        ], 'None') }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-6 card">
            <div class="card-header">
                Remove Role from User
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'AdminController@removeRole', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('user', 'User') }}
                    {{ Form::text('user', '', ['class' => 'form-control', 'placeholder' => 'Character Name']) }}
                    {{ Form::select('role', [
                        'None' => 'None',
                        'Guest' => 'Guest',
                        'User' => 'User',
                        'Admin' => 'Admin',
                        'SuperUser' => 'SuperUser',
                        ], 'None') }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 card">
            <div class="card-header">
                Add Permission for User
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'AdminController@addPermission', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('user', 'User') }}
                    {{ Form::text('user', '', ['class' => 'form-control', 'placeholder' => 'Character Name']) }}
                    {{ Form::select('permission', [
                        'None' => 'None',
                        'logistics.minion' => 'logistics.minion',
                        'structure.operator' => 'structure.operator',
                        ], 'None') }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection