@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    {!! Form::open(['action' => 'Blacklist\BlacklistController@SearchInBlacklist', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('parameter', 'Seach Parameter') }}
        {{ Form::text('parameter', '', ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Search', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection