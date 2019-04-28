@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Public Contracts</h2>
    </div>
</div>
<br>
@if(count($data['contracts']))
    @include('contracts.includes.contractsdisplay')
@else
    @include('contracts.includes.nocontracts')
@endif
@endsection