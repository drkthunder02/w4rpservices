@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Update Existing Moon</h2>
        </div>
        <div class="card-body">
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
                {{ Form::label('contact_type', 'Contact Type') }}
                {{ Form::select('contact_type', ['Character' => 'Character', 'Corporation' => 'Corporation'], 'Character') }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('rental_end', 'Rental End Date') }}
                {{ Form::date('rental_end', \Carbon\Carbon::now()->endOfMonth(), ['class' => 'form-control']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('paid_until', 'Paid Until') }}
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
    </div>
</div>
@endsection