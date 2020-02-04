@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Update Existing Moon</h2>
    {!! Form::open(['action' => 'Moons\MoonsAdminController@storeUpdateMoon', 'method' => 'POST']) !!}
    <div class="form-group col-md-6">
        {{ Form::label('spmn', 'Moon') }}
        {{ Form::select('spmn', $spmn, null, ['class' => 'form-control', 'placeholder' => 'Select Moon...']) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('contact', 'Contact') }}
        {{ Form::text('contact', '', ['class' => 'form-control', 'placeholder' => 'Character']) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('paid_until', 'Rental End Date') }}
        {{ Form::date('paid_until', \Carbon\Carbon::now()->endOfMonth(), ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
        Paid?<br>
        {{ Form::label('paid', 'No') }}
        {{ Form::radio('paid', 'No', true) }}
        {{ Form::label('paid', 'Yes') }}
        {{ Form::radio('paid', 'Yes') }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection