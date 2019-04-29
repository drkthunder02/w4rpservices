@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Modify Bid</h2>
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $contract['title'] }}
                </div>
                <div class="card-body">
                    Type: {{ $contract['type'] }}<br>
                    End Date:  {{ $contract['end_date'] }}<br>
                    Description:  {{ $contract['body'] }}<br>
                    {!! Form::open(['action' => 'ContractController@modifyBid', 'method' => 'POST']) !!}
                    <div class="form-group">
                        {{ Form::label('bid', 'Bid') }}
                        {{ Form::text('bid', '', ['class' => 'form-control', 'placeholder' => '1.0']) }}
                        {{ Form::label('suffix', 'M') }}
                        {{ Form::radio('suffix', 'M', false) }}
                        {{ Form::label('suffix', 'B') }}
                        {{ Form::radio('suffix', 'B', false) }}
                        {{ Form::hidden('type', $contract['type']) }}
                    </div>
                    {{ Form::submit('Modify Bid', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection