@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Select the Structure To View the Ledger For</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'Moons\MoonLedgerController@displayLedger', 'method' => 'POST']) !!}
            <div class="form-group">
                {{ Form::label('structure', 'Structure') }}
                {{ Form::select('structure', $structures, ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection