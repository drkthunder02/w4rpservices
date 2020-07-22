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
            @if(count($bids) > 0)
                @foreach($bids as $bid)
                <div class="card">
                    <div class="card-header">
                        
                    </div>
                    <div class="card-body">

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