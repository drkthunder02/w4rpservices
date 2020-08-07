@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Supply Chain Contracts</h2>
        <!-- Create button for creating a new contract -->

        <!-- Create button for deleting own contracts -->
    </div>
    <br>
    <div class="row justify-content-center">
        <div class="btn toolbar" role="toolbar" aria-label="Toolbar">
            <div class="btn-group mr-2" role="group" aria-label="Create">
                <a class="btn btn-primary" href="/supplychain/contracts/new" role="button">Create Contract</a>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Delete">
                <a class="btn btn-danger" href="/supplychain/contracts/delete" role="button">Delete Contract</a>
            </div>
        </div>
    </div>
</div>
<br>
@if(count($openContracts))
@foreach($openContracts as $contract)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm" align="left">
                            {{ $contract['title'] }}
                        </div>
                        <div class="col-sm" align="right">
                            {!! Form::open(['action' => 'Contracts\SupplyChainController@displaySupplyChainContractBid', 'method' => 'POST']) !!}
                            {{ Form::hidden('contract_id', $contract['contract_id'], ['class' => 'form-control']) }}
                            {{ Form::submit('Bid', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        Delivery Date: {{ $contract['delivery_by'] }}<br>
                        End Date: {{ $contract['end_date'] }}<br>
                    </div>
                    <span class="border-dark">
                        <div class="container">
                            {!! $contract['body'] !!}
                        </div>
                    </span>
                    <hr>
                    <!-- If there is more than one bid display the lowest bid, and the number of bids -->
                    @if($contract['bid_count'] > 0)
                        <span class="border-dark">
                            <table class="table table-striped">
                                {{ $contract['lowest_bid']['name'] }}<br>
                                {{ $contract['lowest_bid']['amount'] }}<br>
                            </table>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@else
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>No Open Supply Chain Contracts</h2>
                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if(count($closedContracts))
    @include('supplychain.includes.closedcontracts')
@else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>No Open Personal Supply Chain Contracts</h2>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection