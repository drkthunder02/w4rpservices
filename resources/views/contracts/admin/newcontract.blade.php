@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Test Page</h2>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    New Contracts
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'ContractAdminController@storeNewContract', 'method' => 'POST']) !!}
                        <div class="form-group">
                            {{ Form::label('name', 'Contract Name') }}
                            {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Some Name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('description', 'Description') }}
                            {{ Form::textarea('description', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('date', 'End Date') }}
                            {{ Form::text('date', '', ['class' => 'form-control', 'placeholder' => '4/24/2019']) }}
                        </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container col-md-12">
    <div class="card">
        <div class="card-header">
            Current Top Bid
        </div>
        <div class="card-body">
            Some corporation and the price
        </div>
    </div>
</div>
@endsection