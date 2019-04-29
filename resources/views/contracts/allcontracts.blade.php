@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>All Contracts</h2>
    </div>
</div>
<br>
@if(count($contracts))
    @include('contracts.includes.all')
@else
    @include('contracts.includes.nocontracts')
@endif
@endsection