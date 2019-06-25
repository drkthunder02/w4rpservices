@extends('admin.layouts.b4')
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
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Modify Role</h2>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection