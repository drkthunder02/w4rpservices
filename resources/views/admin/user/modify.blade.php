@extends('layouts.admin.b4')
@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <h2>Modify User Section</h2>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <h3>User: {{ $user->name }}</h3>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3>Add Permission</h3>
                </div>
                <div class="card-body">
                    {{ Form::open(['action' => 'Dashboard\AdminDashboardController@addPermission', 'method' => 'POST']) }}
                    <div class="form-group">
                    {{ Form::hidden('user', $user->character_id) }}
                    {{ Form::select('permission', $permissions, 'None', ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-2">
                        {{ Form::submit('Add Permission', ['class' => 'btn btn-primary']) }}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Modify Role</h2>
                </div>
                <div class="card-body">
                    <div class="container">
                        <h2>Current Role: {{ $role }}</h2>
                    </div>
                    {{ Form::open(['action' => 'Dashboard\AdminDashboardController@modifyRole', 'method' => 'POST']) }}
                    <div class="form-group">
                    {{ Form::hidden('user', $user->character_id) }}
                    {{ Form::select('role', $roles, 'None', ['class' => 'form-control']) }}
                    {{ Form::hidden('type', 'modifyRole') }}
                    </div>
                    <div class="form-group col-md-2">
                        {{ Form::submit('Modify Role', ['class' => 'btn btn-primary']) }}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection