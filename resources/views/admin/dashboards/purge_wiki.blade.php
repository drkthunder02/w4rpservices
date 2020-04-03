@extends('layouts.admin.b4')
@section('content')
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
@endsection