@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Supply Chain Contracts</h2>
    </div>
</div>
<br>
@if(count($contracts))
    @include('supplychain.includes.contracts')
@else
    @include('supplychain.includes.nocontracts')
@endif
@endsection