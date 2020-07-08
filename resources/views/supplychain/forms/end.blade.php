@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <h2>Supply Chain Contract Completion</h2>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <table class="table table-striped table-bordered">
                        <!-- Card Header display the supply chain contract information -->
                        <thead>
                            <th>Supply Chain Contract Id</th>
                            <th>Title</th>
                            <th>End Date</th>
                            <th>Delivery Date</th>
                            <th>Description</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $contract['contract_id'] }}</td>
                                <td>{{ $contract['title'] }}</td>
                                <td>{{ $contract['end_date'] }}</td>
                                <td>{{ $contract['delivery_by'] }}</td>
                                <td>{{ $contract['body'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Card Body display all of the bids and allows for selection of the bid for the supply chain contract -->
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th>Bid Amount</th>
                            <th>Entity</th>
                            <th>Notes</th>
                            <th>Accept?</th>
                        </thead>
                        <tbody>
                            {!! Form::open(['action' => 'Contracts\SupplyChainController@storeEndSupplyChainContract']) !!}
                            {{ Form::hidden('contract_id', $contract['contract_id']) }}
                            @foreach($bids as $bid)
                            <tr>
                                <td>{{ $bid['amount'] }}</td>
                                <td>{{ $bid['name'] }}</td>
                                <td><pre>{{ $bid['notes'] }}</pre></td>
                                <td>{{ Form::radio('accept', $bid['id'], false, ['class' => 'form-control']) }}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ Form::submit('End Contract', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection