@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Supply Chain Contracts</h2>
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
    @include('supplychain.includes.opencontracts')
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
                    <h2>No Closed Supply Chain Contracts</h2>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection