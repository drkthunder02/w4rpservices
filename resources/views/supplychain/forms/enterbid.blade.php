@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Bid on Contract</h2>
    </div>
</div>  
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Enter Bid
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Contracts\SupplyChainController@storeSupplyChainContractBid', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('bid', 'Bid') }}
                        {{ Form::text('bid', '', ['class' => 'form-control', 'placeholder' => '0.00']) }}
                        {{ Form::hidden('contract_id', $contractId) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('notes', 'Notes') }}
                        {{ Form::textarea('notes', '', ['class' => 'form-control']) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection