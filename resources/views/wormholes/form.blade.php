@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card">
            <div class="card-header">
                <h2>Enter Wormhole Info</h2>
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'Wormholes\WormholeController@storeWormhole', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('system', 'System') }}
                    {{ Form::text('system', '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('sig', 'Sig ID') }}
                    {{ Form::text('sig', '', ['class' => 'form-control', 'placeholder' => 'XXX-XXX']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('duration', 'Duration Left') }}
                    {{ Form::select('duration', $duration, null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('dateTime', 'Date Scanned') }}
                    {{ Form::date('dateTime', \Carbon\Carbon::now(), ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('class', 'WH Class') }}
                    {{ Form::select('class', $class, null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('type', 'WH Type') }}
                    {{ Form::select('class', $type, null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('size', 'WH Size') }}
                    {{ Form::select('size', $size, null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('stability', 'Stability') }}
                    {{ Form::select('stability', $stability, null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('points', 'Points of Interest') }}
                    {{ Form::textarea('points', null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('link', 'WH Link') }}
                    {{ Form::text('link', null, ['class' => 'form-control']) }}
                </div>
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection