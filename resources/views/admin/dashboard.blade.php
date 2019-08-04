@extends('admin.layouts.b4')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <h2> Admin Dashboard</h2>
    </div>
</div>
<br>
<nav>
<ul class="nav nav-tabs">
    <li class="nav-item active"><a class="nav-link active" data-toggle="tab" href="#user">User</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#logins">Login</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#wiki">Wiki</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#taxes">Taxes</a></li>
</ul>
<br>
<div class="tab-content">
    <div id="user" class="tab-pane active">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    User Information
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
                            @foreach($userArr as $user)
                            <tr>
                                <td>{{ $user['name'] }}</td>
                                <td>{{ $user['role'] }}</td>
                                <td>
                                @if($user['permissions'])
                                @foreach($user['permissions'] as $perm)
                                    {{ implode(', ', $perm) }}
                                @endforeach
                                @else
                                    None
                                @endif
                                </td>
                                <td>
                                    {!! Form::open(['action' => 'Dashboard\AdminController@displayModifyUser', 'method' => 'POST']) !!}
                                    {{ Form::hidden('user', $user['name']) }}
                                    {{ Form::submit('Modify User', ['class' => 'btn btn-primary']) }}
                                    {!! Form::close() !!}
                                    {!! Form::open(['action' => 'Dashboard\AdminController@removeUser', 'method' => 'POST']) !!}
                                    {{ Form::hidden('user', $user['name']) }}
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
    </div>
    <div id="logins" class="tab-pane fade">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Add Allowed Login
                        </div>
                        <div class="card-body">
                            {!! Form::open(['action' => 'Dashboard\AdminController@addAllowedLogin', 'method' => 'POST']) !!}
                            <div class="form-group">
                                {{ Form::label('allowedEntityId', 'Allowed Entity ID') }}
                                {{ Form::text('allowedEntityId', '', ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('allowedEntityType', 'Allowed Entity Type') }}
                                {{ Form::select('allowedEtntityType', ['Corporation' => 'Corporation', 'Alliance' => 'Alliance'], null, ['class' => 'form-control']) }}
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
                            {!! Form::open(['action' => 'Dashboard\AdminController@removeAllowedLogin', 'method' => 'POST']) !!}
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
    </div>
    <div id="wiki" class="tab-pane fade">
        <div class="container">
            <div class="row">
                {!! Form::open(['action' => 'Wiki\WikiController@purgeUsers', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('admin', 'This action will log the administrator who peformed the action.') }}
                    {{ Form::hidden('admin', auth()->user()->character_id) }}
                </div>
                {{ Form::submit('Purge Wiki', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div id="taxes" class="tab-pane fade">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            PI Taxes
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>PI Taxes</th>
                                </thead>
                                <tbody>
                                    @foreach($pis as $pi)
                                        <tr>
                                            <td>{{ $pi['date'] }}</td>
                                            <td>{{ $pi['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Office Taxes
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>Office Taxes</th>
                                </thead>
                                <tbody>
                                    @foreach($offices as $office)
                                        <tr>
                                            <td>{{ $office['date'] }}</td>
                                            <td>{{ $office['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Industry Taxes
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>Industry Taxes</th>
                                </thead>
                                <tbody>
                                    @foreach($industrys as $industry)
                                        <tr>
                                            <td>{{ $industry['date'] }}</td>
                                            <td>{{ $industry['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Reprocessing Taxes
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>Reprocessing Taxes</th>
                                </thead>
                                <tbody>
                                    @foreach($reprocessings as $reprocessing)
                                        <tr>
                                            <td>{{ $reprocessing['date'] }}</td>
                                            <td>{{ $reprocessing['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Market Taxes
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>Market Taxes</th>
                                </thead>
                                <tbody>
                                    @foreach($markets as $market)
                                        <tr>
                                            <td>{{ $market['date'] }}</td>
                                            <td>{{ $market['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Jump Gate Taxes
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>Jump Gate Taxes</th>
                                </thead>
                                <tbody>
                                    @foreach($jumpgates as $jumpgate)
                                        <tr>
                                            <td>{{ $jumpgate['date'] }}</td>
                                            <td>{{ $jumpgate['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            PI Transactions
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>PI Transactions</th>
                                </thead>
                                <tbody>
                                    @foreach($pigross as $pi)
                                        <tr>
                                            <td>{{ $pi['date'] }}</td>
                                            <td>{{ $pi['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            SRP Actual Paid Out
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>SRP Actual</th>
                                </thead>
                                <tbody>
                                    @foreach($srpActual as $srp)
                                        <tr>
                                            <td>{{ $srp['date'] }}</td>
                                            <td>{{ $srp['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            SRP Loss Values
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Month</th>
                                    <th>SRP Loss</th>
                                </thead>
                                <tbody>
                                    @foreach($srpLoss as $srp)
                                        <tr>
                                            <td>{{ $srp['date'] }}</td>
                                            <td>{{ $srp['gross'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection