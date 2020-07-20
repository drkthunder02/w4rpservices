@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Modify Bid
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Contracts\SupplyChainController@storeModifyBid', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::hidden('bid_id', $bidId) }}
                        {{ Form::hidden('contract_id', $contractId) }}
                        {{ Form::label('bid_amount', 'Bid Amount') }}
                        {{ Form::text('bid_amount', '', ['class' => 'form-control']) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection