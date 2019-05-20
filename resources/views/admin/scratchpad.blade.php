<div class="container">
    <div class="row">
        <table class="table table-striped">
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
                    @foreach($user['permissions'] as $perm)
                        {{ implode(', ', $perm) }}
                    @endforeach
                    </td>
                    <td>
                        {!! Form::open(['action' => 'Dashboard\AdminController@removeUser', 'method' => 'POST']) !!}
                        {{ Form::hidden('user', $user['name']) }}
                        {{ Form::submit('Remove User', ['class' => 'btn btn-primary']) }}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        <table>
    </div>
</div>