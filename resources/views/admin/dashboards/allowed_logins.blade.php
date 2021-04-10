@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Add Allowed Login
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminDashboardController@addAllowedLogin', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('allowedEntityId', 'Allowed Entity ID') }}
                        {{ Form::text('allowedEntityId', '', ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('allowedEntityType', 'Allowed Entity Type') }}
                        {{ Form::select('allowedEntityType', ['Corporation' => 'Corporation', 'Alliance' => 'Alliance'], null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('allowedEntityName', 'Allowed Entity Name') }}
                        {{ Form::text('allowedEntityName', '', ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('allowedLoginType', 'Allowed Login Type') }}
                        {{ Form::select('allowedLoginType', ['Legacy' => 'Legacy', 'Renter' => 'Renter'], null, ['class' => 'form-control']) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Remove Allowed Login
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminDashboardController@removeAllowedLogin', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('removeAllowedLogin', 'Remove Entity') }}
                        {{ Form::select('removeAllowedLogin', $entities, null, ['class' => 'form-control']) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection