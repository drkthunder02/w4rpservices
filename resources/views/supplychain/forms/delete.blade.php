@extends('layouts.user.dashb4')
@section('content')
<!-- Delete a contract the user has created -->
<div class="container">
    <div class="row justify-content-center">
        @if(count($contracts) > 0)
            @foreach($contracts as $contract)
            <div class="card">
                <div class="card-header">
                    <h2>{{ $contract['title'] }}</h2>
                </div>
                <div class="card-body">
                    {!! $contract['body'] !!}<br>
                    {!! Form::open(['action' => 'Contracts\SupplyChainController@deleteSupplyChainContract', 'method' => 'POST']) !!}
                    {{ Form::hidden('contractId', $contract['contract_id']) }}
                    {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                    {!! Form::close() !!}
                </div>
            </div>
            @endforeach
        @else
            <div class="card">
                <div class="card-header">
                    <h2>User currently has no supply chain contracts open.</h2>
                </div>
                <div class="card-body">
                    <div class="container">

                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection