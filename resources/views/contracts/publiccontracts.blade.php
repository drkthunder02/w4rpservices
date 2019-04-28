@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Public Contracts</h2>
    </div>
</div>
<br>
@if(count($data))
@foreach($data as $contract)
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm" align="left">
                                {{ $contract['title'] }}
                            </div>
                            <div class="col-sm" align="center">
                                Type: Public
                            </div>
                            <div class="col-sm" align="right">
                                <a href="/contracts/display/newbid/{{ $contract['contract_id'] }}" class="btn btn-primary" role="button">Bid on Contract</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            End Date: {{ $contract['end_date'] }}
                        </div>
                        <span class="border-dark">
                            <div class="container">
                                {{ $contract['body'] }}
                            </div>
                        </span>
                        <hr>
                        <span class="border-dark">
                            @if(count($data['bids']))
                            <table class="table table-striped">
                                    <thead>
                                        <th>Corporation</th>
                                        <th>Amount</th>
                                    </thead>
                                    <tbody>
                                    @foreach($data['bids'] as $bid)
                                        <tr>
                                            <td>{{ $bid['corporation_name'] }}</td>
                                            <td>{{ $bid['bid_amount'] }}</td>
                                            @if(auth()->user()->character_id == $bid['character_id'])
                                                {{ Form::open(['action' => 'ContractController@displayModifyBid', 'method' => 'POST']) }}
                                                    {{ Form::hidden('id', $bid['id']) }}
                                                    {{ Form::hidden('contract_id', $bid['contract_id']) }}
                                                    {{ Form::submit('Modify Bid', ['class' => 'btn btn-primary']) }}
                                                {!! Form::close() !!}
                                
                                                {{ Form::open(['action' => 'ContractController@deleteBid', 'method' => 'POST']) }}
                                                    {{ Form::hidden('id', $bid['id']) }}
                                                    {{ Form::hidden('contract_id', $bid['contract_id']) }}
                                                    {{ Form::submit('Delete Bid', ['class' => 'btn btn-primary']) }}
                                                {!! Form::close() !!}
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                No Bids have been entered.
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
<br>
@else
    @include('contracts.includes.nocontracts')
@endif
@endsection