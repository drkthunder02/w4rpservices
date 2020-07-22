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
    @include('supplychain.includes.opencontracts')
@else
    @include('supplychain.includes.nocontracts')
@endif
@if(count($closedContracts))
    @include('supplychain.includes.closedcontracts')
@else
    @include('supplychain.includes.nocontracts')
@endif

@endsection