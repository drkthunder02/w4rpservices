@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>New Rental Contract</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'SystemRentals\RentalAdminController@addRentalSystem', 'method' => 'POST']) !!}
            <div class="form-group">
                {{ Form::label('contact_name', 'Contact Name') }}
                {{ Form::text('contact_name', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('contact_corp_name', 'Corporation Name') }}
                {{ Form::text('contact_corp_name', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('system', 'System') }}
                {{ Form::text('system', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('rental_cost', 'Rental Cost') }}
                {{ Form::text('rental_cost', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('paid_until', 'Paid Until') }}
                {{ Form::date('paid_until', \Carbon\Carbon::now()->addMonth(), ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Add Rental', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection