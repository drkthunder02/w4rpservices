@extends('layouts.admin.b4')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <h2>Wiki Admin Dashboard</h2>
    </div>
</div>
<br>
<div class="container">
    <div class="row">
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Add User to Group</h2>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminDashboardController@addWikiUserGroup', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('user', 'Select a User') }}
                        {{ Form::select('user', $wikiUsers, null, ['placeholder' => 'Pick A User']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('groupname', 'Group') }}
                        {{ Form::select('groupname', $wikiGroups, null, ['placeholder' => 'Pick A Group']) }}
                    </div>
                    {{ Form::submit('Add User To Grouop', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Create New User Group</h2>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminDashboardController@insertNewWikiUserGroup', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('group', 'New Group Name') }}
                        {{ Form::text('group', '', ['class' => 'form-control col-md-4']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('description', 'Group Description') }}
                        {{ Form::text('description', '', ['class' => 'form-control col-md-4']) }}
                    </div>
                    {{ Form::submit('Add New Group', ['class' => 'btn btn-success']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Remove User From Group</h2>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminController@removeWikiUserGroup', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('user', 'Select a User') }}
                        {{ Form::select('user', $wikiUsers, null, ['placeholder' => 'Pick A User']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('groupname', 'Group') }}
                        {{ Form::select('groupname', $wikiGroups, null, ['placeholder' => 'Pick A Group']) }}
                    </div>
                    {{ Form::submit('Remove User From Group', ['class' => 'btn btn-warning']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Remove User From All Groups</h2>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminController@removeWikiUserAllGroups', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('user', 'Select User') }}
                        {{ Form::select('user', $wikiUsers, null, ['placeholder' => 'Select User']) }}
                    </div>
                    {{ Form::submit('Remove User From All Groups', ['class' => 'btn btn-danger']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Remove Wiki User</h2>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminController@deleteWikiUser', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('user', 'Select User') }}
                        {{ Form::select('user', $wikiUsers, null, ['placeholder' => 'Select User to Delete']) }}
                    </div>
                    {{ Form::submit('Delete User', ['class' => 'btn btn-danger']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Purge Wiki</h2>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Dashboard\AdminController@purgeWikiUsers', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('admin', 'This action will log the administrator who peformed the action.') }}
                        {{ Form::hidden('admin', auth()->user()->character_id) }}
                    </div>
                    {{ Form::submit('Purge Wiki', ['class' => 'btn btn-danger']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection