@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Past Contracts Dashboard</h2>
    </div>
</div>
<br>
@if(count($contracts))
@foreach($contracts as $contract)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm" align="left">
                            {{ $contract['title'] }}
                        </div>
                        <div class="col-sm" align="center">
                            Type: {{ $contract['type'] }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        End Date: {{ $contract['end_date'] }}
                    </div>
                    <div class="container">
                        Accepted Bid Amount: {{ $contract['accepted']['bid_amount'] }}<br>
                    </div>
                    <div class="container">
                        Accepted Bid Notes:  {{ $contract['accepted']['notes'] }}
                    </div>
                    <span class="border-dark">
                        <div class="container">
                            {!! $contract['body'] !!}
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