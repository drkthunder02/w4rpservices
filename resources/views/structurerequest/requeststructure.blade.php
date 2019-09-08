@extends('layouts.b4')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Structure Request Form</h2>
    </div>
    <div class="card-body">
        {!! Form::open(['action' => 'Logistics\StructureRequestController@storeForm', 'method' => 'POST']) !!}
        <div class="form-group">
            {{ Form::label('corporation_name', 'Corporation Name') }}
            {{ Form::text('corporation_name', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('system', 'System') }}
            {{ Form::text('system', '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('structure_size', 'Structure Size') }}
            {{ Form::select('structure_size', ['M', 'L', 'XL'], null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('structure_type', 'Structure Type') }}
            {{ Form::select('structure_type', ['Flex', 'Citadel', 'Refinery', 'Engineering'], null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('requested_drop_time', 'Requested Drop Time') }}
            {{ Form::dateTime('requested_drop_time', \Carbon\Carbon::now(), ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('requester', 'Requester') }}
            {{ Form::text('requester', '', ['class' => 'form-control']) }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
        {!! Form::close() !!}
    </div>
</div>
@endsection