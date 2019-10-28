@extends('layouts.b4')
@section('content')
<div class="container">
    {!! Form::open(['action' => 'Corps\BlacklistController@AddToBlacklist', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('name', 'Character Name') }}
        {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'CCP Antiquarian']) }}
    </div>
    <div class="form-group">
        {{ Form::label('reason', 'Reason') }}
        {{ Form::textarea('reason', '', ['class' => 'form-control', 'placeholder' => 'Just another antiquated dev.']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection