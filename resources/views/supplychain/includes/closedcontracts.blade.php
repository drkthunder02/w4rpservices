@foreach($closedContracts as $contract)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm" align="left">
                            {{ $contract['title'] }}
                        </div>
                        <div class="col-sm" align="right">
                            {!! Form::open(['action' => 'Contracts\SupplyChainContract@displaySupplyChainContractBid', 'method' => 'POST']) !!}
                            {{ Form::hidden('contract_id', $contract['contract_id'], ['class' => 'form-control']) }}
                            {{ Form::submit('Bid', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        Delivery Date: {{ $contract['delivery_by'] }}<br>
                        End Date: {{ $contract['end_date'] }}<br>
                    </div>
                    <span class="border-dark">
                        <div class="container">
                            {!! $contract['body'] !!}
                        </div>
                    </span>
                    <hr>
                    <!-- If there is more than one bid display the lowest bid, and the number of bids -->
                    @if($contract['bid_count'] > 0)
                        <span class="border-dark">
                            <table class="table table-striped">
                                {{ $contract['lowest_bid']['name'] }}<br>
                                {{ $contract['lowest_bid']['amount'] }}<br>
                            </table>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach