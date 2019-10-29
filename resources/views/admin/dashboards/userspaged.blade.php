@extends('admin.layouts.b4')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2>User Information</h2>
        </div>
        <div class="card-body">
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
                        <td>{{ $user->getRole() }}</td>
                        <td>
                        {{ $perms = $user->getPermissionsArray() }}                            
                        @if($perms)
                            @foreach($perms as $perm)
                                {{ implode(', ', $perm) }}
                            @endforeach
                        @else
                            No Permissions
                        @endif
                        </td>
                        <td>
                            {!! Form::open(['action' => 'Dashboard\AdminController@displayModifyUser', 'method' => 'POST']) !!}
                            {{ Form::hidden('user', $user->name) }}
                            {{ Form::submit('Modify User', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                            {!! Form::open(['action' => 'Dashboard\AdminController@removeUser', 'method' => 'POST']) !!}
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
</div>
{{ $usersArr->links() }}
@endsection