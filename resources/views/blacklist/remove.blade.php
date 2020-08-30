@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    {!! Form::open(['action' => 'Blacklist\BlacklistController@RemoveFromBlacklist', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('name', 'Entity Name') }}
        {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'CCP Antiquarian']) }}
    </div>
    <div class="form-group">
        {{ Form::label('notes', 'Notes') }}
        {{ Form::textarea('notes', '', ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection