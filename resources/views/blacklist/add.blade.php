@extends('layouts.b4')
@section('content')
<div class="container">
    {!! Form::open(['action' => 'Blacklist\BlacklistController@AddToBlacklist', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('name', 'Character Name') }}
        {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'CCP Antiquarian']) }}
    </div>
    <div class="form-group">
        {{ Form::label('reason', 'Reason') }}
        {{ Form::textarea('reason', 'N/A', ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection