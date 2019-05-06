<table class="table table-striped">
    <thead>
        <th>Corporation</th>
        <th>Amount</th>
    </thead>
    <tbody>
    @foreach($data['bids'] as $bid)
        <tr>
            <td>{{ $bid['corporation_name'] }}</td>
            <td>{{ $bid['bid_amount'] }}</td>
            @if(auth()->user()->character_id == $bid['character_id'])
                {{ Form::open(['action' => 'Contracts\ContractController@displayModifyBid', 'method' => 'POST']) }}
                    {{ Form::hidden('id', $bid['id']) }}
                    {{ Form::hidden('contract_id', $bid['contract_id']) }}
                    {{ Form::submit('Modify Bid', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}

                {{ Form::open(['action' => 'Contracts\ContractController@deleteBid', 'method' => 'POST']) }}
                    {{ Form::hidden('id', $bid['id']) }}
                    {{ Form::hidden('contract_id', $bid['contract_id']) }}
                    {{ Form::submit('Delete Bid', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            @endif
        </tr>
    @endforeach
    </tbody>
</table>