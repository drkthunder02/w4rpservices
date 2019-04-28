@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Test Page</h2>
</div>
<br>
@if(count($contracts))
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <p align="text-left">
                            {{ $contract['title'] }}
                        </p>
                        <p align="text-center">
                            Type: Public
                        </p>
                        <p align="text-right">
                            {!! Form::open(['action' => 'ContractController@displayBid', 'method' => 'POST']) !!}
                            {{ Form::hidden('contract_id', $contract['contract_id']) }}
                            {{ Form::submit('Bid', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </p>
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
    <br>
@else
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        No Contracts Issued
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection