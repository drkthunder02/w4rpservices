@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Bid on Contract</h2>
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Enter Bid
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'ContractController@storeBid', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('bid', 'Bid') }}
                        {{ Form::text('bid', '', ['class' => 'form-control', 'placeholder' => '1.0B']) }}
                        {{ Form::hidden('contract_id', $contractId) }}
                        {{ Form::label('suffix', 'M') }}
                        {{ Form::radio('suffix', 'M', false) }}
                        {{ Form::label('suffix', 'B') }}
                        {{ Form::radio('suffix', 'B', false) }}
                    </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection