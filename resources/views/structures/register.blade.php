@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Register Structure</h2>
    {!! Form::open(['action' => 'RegisterStructureController@storeStructure', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('region', 'Region') }}
        {{ Form::text('region', '', ['class' => 'form-control']) }}
        {{ Form::label('system', 'System') }}
        {{ Form::text('system', '', ['class' => 'form-control']) }}
        {{ Form::label('structure_name', 'Structure Name') }}
        {{ Form::text('structure_name', '', ['class' => 'form-control']) }}
        {{ Form::label('tax', 'Tax') }}
        {{ Form::text('tax', '', ['class' => 'form-control']) }}
        {{ Form::label('structure_type', 'Structure Type') }}
        {{ Form::select('structure_type', ['Refinery' => 'Refinery', 'Citadel' => 'Citadel'], null, ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection