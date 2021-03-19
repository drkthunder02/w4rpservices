@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Information</h2>
        </div>
        <div class="card-body">
            This page displays the Scopes, Permissions, and Roles you currently have on the services site.<br>
            By clicking on the Register Alt link you can add alts for SRP management.<br>
        </div>
    </div>
</div>
<br>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Scopes</h2>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th>Scope</th>
                        </thead>
                        <tbody>
                            @if($scopeCount > 0) 
                                @foreach($scopes as $scope)
                                <tr>
                                    <td>{{ $scope->scope }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>No Scopes</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Permissions</h2>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th>Permission</th>
                        </thead>
                        <tbody>
                            @if($permissionCount > 0) 
                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->permission }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>No Permissions</td>
                                    </tr>
                                @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Roles</h2>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th>Role</th>
                        </thead>
                        <tbody>
                            @if($roleCount > 0) 
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->role }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>No Role</td>
                                    </tr>
                                @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container">
    <table class="table table-striped table-bordered">
        <thead>
            <th>Alt Name</th>
            <th>Character Id</th>
            <th>Remove</th>
        </thead>
        <tbody>
            @if($altCount > 0)
                @foreach($alts as $alt)
                {{ Form::open(['action' => 'Dashboard\DashboardController@removeAlt', 'method' => 'POST']) }}
                <tr>
                    <td>{{ $alt->name }}</td>
                    <td>{{ $alt->character_id }}</td>
                    <td>
                        {{ Form::hidden('character', $alt->character_id) }}
                        {{ Form::submit('Remove Alt', ['class' => 'btn btn-primary']) }}
                    </td>
                </tr>
                {{ Form::close() }}
                @endforeach
            @else
                <tr>
                    <td>No Alts on Record</td>
                    <td> </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Register Alts for SRP Form and Mining Taxes</h2>
        </div>
        <div class="card-body">
            <a href="/login"><img src="{{asset('/img/eve-sso-login-white-large.png')}}"></a>
        </div>
    </div>
</div>
<br>
@endsection