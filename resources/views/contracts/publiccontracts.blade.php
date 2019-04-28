@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Public Contracts</h2>
    </div>
</div>
<br>

@if(count($data['contracts']))
@foreach($data['contracts'] as $contract)
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm" align="left">
                                Title
                            </div>
                            <div class="col-sm" align="center">
                                Type: Public
                            </div>
                            <div class="col-sm" align="right">
                                Button
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            End Date: 
                        </div>
                        <span class="border-dark">
                            <div class="container">
                                Body
                            </div>
                        </span>
                        <hr>
                        <span class="border-dark">
                            @if(count($data['bids']))
                                Bids
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
    @include('contracts.includes.nocontracts')
@endif
@endsection