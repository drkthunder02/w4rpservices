@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Create New Supply Chain Contract</h2>
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Enter New Supply Chain Contract Information
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Contracts\SupplyChainController@storeNewSupplyChainContract', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('name', 'Contract Name') }}
                        {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Supply Chain Contract Name']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('body', 'Description') }}
                        {{ Form::text('body', '', ['class' => 'form-control', 'placeholder' => 'Enter description.']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('date', 'End Date') }}
                        {{ Form::label('date', \Carbon\Carbon::now()->addWeek(), ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('delivery', 'Delivery Date') }}
                        {{ Form::label('delivery', \Carbon\Carbon::now()->addWeeks(2), ['class' => 'form-control']) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection