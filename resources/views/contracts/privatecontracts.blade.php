@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Test Page</h2>
</div>
<br>
@if($contracts != null)
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <p align="text-left">
                            {{ $contract['title'] }}
                        </p>
                        <p align="text-center">
                            Type: Private
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