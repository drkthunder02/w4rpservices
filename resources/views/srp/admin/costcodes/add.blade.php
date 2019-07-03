@extends('srp.layouts.b4')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Add a New Cost Code</h2>
    </div>
    <div class="card-body">
        {!! Form::open(['action' => 'SRP\SRPAdminController@addCostCode', 'method' => 'POST']) !!}
        <div class="form-group">
            {{ Form::label('code', 'Code') }}
            {{ Form::text('code', null, ['class' => 'form-control', 'placeholder' => 'T1']) }}
        </div>
        <div class="form-group">
            {{ Form::label('description', 'Description') }}
            {{ Form::text('description', null, ['class' => 'form-control', 'placeholder' => 'description']) }}
        </div>
        <div class="form-group">
            {{ Form::label('payout', 'Payout') }}
            {{ Form::text('payout', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::submit('Add Payout Code', ['class' => 'btn btn-primary']) }}
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection