@extends('layouts.b4')
@section('content')
<br>
<div class="container">
    {!! Form::open(['action' => 'Blacklist\BlacklistController@AddToBlacklist', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('name', 'Character Name') }}
        {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'CCP Antiquarian']) }}
    </div>
    <div class="form-group">
        {{ Form::label('reason', 'Reason') }}
        {{ Form::textarea('reason', '', ['class' => 'form-control', 'placeholder' => 'Just another antiquated dev.']) }}
    </div>
    <div class="form-group">
        {{ Form::label('alts', 'Known Alts') }}
        {{ Form::textarea('alts', '', ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection