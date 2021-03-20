@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>User Information</h2>
        </div>
        <div class="card-body">
            <div class="container">
                {!! Form::open(['action' => 'Dashboard\AdminDashboardController@searchUsers', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('parameter', 'Seach For A User') }}
                    {{ Form::text('parameter', '', ['class' => 'form-control', 'placeholder' => 'CCP Antiquarian']) }}
                </div>
                {{ Form::submit('Search', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach($usersArr as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->permission }}</td>
                        <td>
                            {!! Form::open(['action' => 'Dashboard\AdminDashboardController@displayModifyUser', 'method' => 'POST']) !!}
                            {{ Form::hidden('user', $user->name) }}
                            {{ Form::submit('Modify User', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                            {!! Form::open(['action' => 'Dashboard\AdminDashboardController@removeUser', 'method' => 'POST']) !!}
                            {{ Form::hidden('user', $user->name) }}
                            {{ Form::submit('Remove User', ['class' => 'btn btn-danger']) }}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>
    {{ $usersArr->links() }}
</div>
@endsection