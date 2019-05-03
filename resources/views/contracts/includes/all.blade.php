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
                            <pre>
                                {{ $contract['body'] }}
                            </pre>
                        </div>
                    </span>
                    <hr>
                    <!--  Count the number of bids for the current contract -->
                    @if($contract['bid_count'] > 0)
                        <span class="border-dark">
                            @if($contract['type'] == 'Public')
                            <table class="table table-striped">
                                <thead>
                                    <th>Corporation</th>
                                    <th>Amount</th>
                                    <th></th>
                                </thead>
                                <tbody>
                                @foreach($contract['bids'] as $bid)
                                    <tr>
                                        <td>{{ $bid['corporation_name'] }}</td>
                                        <td>{{ $bid['bid_amount'] }}</td>
                                        @if(auth()->user()->character_id == $bid['character_id'])
                                        <td>
                                            <a href="/contracts/modify/bid/{{ $bid['id'] }}" class="btn btn-primary" role="button">Modify Bid</a>
                                            <a href="/contracts/delete/bid/{{ $bid['id'] }}" class="btn btn-primary" role="button">Delete Bid</a>
                                        </td>
                                        @else
                                        <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                                @foreach($contract['bids'] as $bid)
                                @if(auth()->user()->character_id == $bid['character_id'])
                                    <a href="/contracts/modify/bid/{{ $bid['id'] }}" class="btn btn-primary" role="button">Modify Bid</a>
                                    <a href="/contracts/delete/bid/{{ $bid['id'] }}" class="btn btn-primary" role="button">Delete Bid</a>
                                @endif
                                @endforeach
                            @endif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endforeach