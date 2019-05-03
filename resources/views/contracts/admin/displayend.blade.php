@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Contract End</h2>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <!-- Card Header display Contract Information -->
                <div class="card-header">
                    <table class="table table-striped">
                        <thead>
                            <th>Contract Id</th>
                            <th>Contract Type</th>
                            <th>Title</th>
                            <th>End Date</th>
                            <th>Description</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $contract['contract_id'] }}</td>
                                <td>{{ $contract['type'] }}</td>
                                <td>{{ $contract['title'] }}</td>
                                <td>{{ $contract['end_date'] }}</td>
                                <td>{{ $contract['body'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--  Card Body displays all of the bids and allows for selection of which bid to accept -->
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <th>Bid Amount</th>
                            <th>Character Name</th>
                            <th>Corporation Name</th>
                            <th>Notes</th>
                            <th>Accept?</th>
                        </thead>
                        <tbody>
                            {!! Form::open(['action' => 'ContractAdminController@endContract', 'method' => 'POST']) !!}
                            {{ Form::hidden('contract_id', $contract['contract_id']) }}
                        @foreach($bids as $bid)
                            <tr>
                                <td>{{ $bid['bid_amount'] }}</td>
                                <td>{{ $bid['character_name'] }}</td>
                                <td>{{ $bid['corporation_name'] }}</td>
                                <td>{{ $bid['notes'] }}</td>
                                <td>{{ Form::radio('accept', $bid['id'], false, ['class' => 'form-control']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection