@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Courier Calculation Form</h2>
        </div>
        <div class="card-body">
            {!! Form::open([
                'action' => 'Logistics\LogisticsController@displayContractDetails',
                'method' => 'POST',
            ]) !!}
            <div class="form-group">
                {{ Form::label('route', 'Route') }}
                {{ Form::select('route', $routes, 'None', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('volume', 'Volume') }}
                {{ Form::text('volume', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('collateral', 'Collateral') }}
                {{ Form::text('collateral', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('type', 'Type') }}
                {{ Form::select('type', $type, 'None', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
        </div>
    </div>
</div>
@endsection