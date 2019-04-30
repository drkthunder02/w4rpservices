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
                            Type: Private
                        </div>
                        <div class="col-sm" align="right">
                            <a href="/contracts/display/newbid/{{ $contract['contract_id'] }}" class="btn btn-primary" role="button">Bid on Contract</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        End Date: {{ $contract['end_date'] }}
                    </div>
                    <span class="border-dark">
                        <div class="container">
                            {{ $contract['body'] }}
                        </div>
                    </span>
                    <hr>
                    <span class="border-dark">
                        <div class="container">
                            Lowest Bid:  {{ number_format($contract['lowestbid']['amount'], 2, ',', '.') }}
                        </div>
                    </span>
                    <hr>
                    @foreach($contract['bids'] as $bid)
                    @if(auth()->user()->character_id == $bid['character_id'])
                        <a href="/contracts/modify/bid/{{ $bid['id'] }}" class="btn btn-primary" role="button">Modify Bid</a>
                        <a href="/contracts/delete/bid/{{ $bid['id'] }}" class="btn btn-primary" role="button">Delete Bid</a>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endforeach