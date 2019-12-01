@extends('layouts.b4')
@section('content')
<div class="container">
    {!! Form::open(['action' => 'Blacklist\BlacklistController@SearchInBlacklist', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('name', 'Character Name') }}
        {{ Form::text('name', '', ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Search', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection