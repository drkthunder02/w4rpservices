@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Add New Structure Market</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'Market\MarketController@storeAddStructure', 'method' => 'POST']) !!}
            <div class="form-group">
                {{ Form::label('tax', 'Market Tax') }}
                {{ Form::text('tax', null, ['class' => 'form-control', 'placeholder' => '5.00']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection