@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Contract Dashboard</h2>
    </div>
</div>
<br>
<button href="{{ link_to_route('displayNewContract', 'Create New Contract') }}" type="button" class="btn btn-primary" role="button">Create New Contract</button>
<br>
@if(count($contracts))
@foreach($contracts as $contract)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <p align="text-left">
                        {{ $contract['title'] }}
                    </p>
                    <p align="text-center">
                        Type: {{ $contract['type'] }}
                    </p>
                    <p align="text-right">
                        {!! Form::open(['action' => 'ContractAdminController@deleteContract', 'method' => 'POST']) !!}
                        {{ Form::hidden('contract_id', $contract['contract_id']) }}
                        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
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
@endforeach
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