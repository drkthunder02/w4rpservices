@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Private Contracts</h2>
    </div>
</div>
<br>
@if(count($contracts))
    @include('contracts.includes.contractsdisplay')
@else
    @include('contracts.includes.nocontracts')
@endif
@endsection