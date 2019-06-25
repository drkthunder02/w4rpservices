@extends('layouts.b4')
@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h2>Modify User</h2>
                <h3>User: {{ $user->name }}</h3>
            </div>
            <div class="card-body">
                {{ Form::open(['action' => 'Dashboard\AdminController@addPermission', 'method' => 'POST']) }}
                <div class="form-group">
                {{ Form::hidden('user', $user->name) }}
                {{ Form::select('permission', [
                    'structure.operator' => 'structure.operator',
                    'logistics.minion' => 'logistics.minion',
                    'admin.finance' => 'admin.finance',
                    'contract.admin' => 'contract.admin',
                    'contract.canbid' => 'contract.canbid',
                ], 'None') }}
                {{ Form::hidden('type', 'addPermission') }}
                </div>
                <div class="form-group col-md-2">
                    {{ Form::submit('Add Permission', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2>Modify Role</h2>
                <h3>User: {{ $user->name }}</h3>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
</div>
@endsection