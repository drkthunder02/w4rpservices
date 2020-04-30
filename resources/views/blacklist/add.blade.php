@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Add User to the Blacklist</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'Blacklist\BlacklistController@AddToBlacklist', 'method' => 'POST']) !!}
            <div class="form-group">
                {{ Form::label('name', 'Entity Name') }}
                {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'CCP Antiquarian']) }}
            </div>
            <div class="form-group">
                {{ Form::label('type', 'Entity Type') }}
                {{ Form::select('type', [
                    'Character' => 'Character',
                    'Corporation' => 'Corporation',
                    'Alliance' => 'Alliance',
                ], 'Character', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('reason', 'Reason') }}
                {{ Form::textarea('reason', 'No reason given.', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('alts', 'Known Alts') }}
                {{ Form::textarea('alts', '', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection