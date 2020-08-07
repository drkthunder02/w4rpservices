@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Personal Bids on Contracts</h2>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if($bidsCount > 0)
                @foreach($bids as $bid)
                <div class="card">
                    <div class="card-header">
                        Contract Id: {{ $bid['contract_id'] }}<br>
                        Contract Title: {{ $bid['title'] }}<br>
                        Issuer:  {{ $bid['issuer_name'] }}<br>
                    </div>
                    <div class="card-body">
                        Bid Id:  {{ $bid['bid_id'] }}<br>
                        Bid Amount: {{ $bid['bid_amount'] }}<br>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-header">
                        <h2>No Bids on Open Contracts</h2>
                    </div>
                    <div class="card-body">
                        <h3> </h3>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection