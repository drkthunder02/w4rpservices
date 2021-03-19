@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>After Action Report Form</h2>
        </div>
        <div class="card-body">
            {!! Form::open([
                'action' => 'AfterActionReports\AfterActionReportsController@StoreReport',
                'method' => 'POST',
            ])
            !!}
            <div class="form-group">
                {{ Form::label('location', 'Location') }}
                {{ Form::text('location', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('time', 'Time') }}
                {{ Form::dateTime('time', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('comms', 'Comms') }}
                {{ Form::select('comms', ['W4RP', 'Voltron', 'TEST', 'Other'], ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('doctrine', 'Doctrine') }}
                {{ Form::text('doctrine', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('objective', 'Objective') }}
                {{ Form::text('objective', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('result', 'Result') }}
                {{ Form::select('result', ['Win', 'Loss', 'Neither'], ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('summary', 'Summary') }}
                {{ Form::textarea('summary', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('improvements', 'Improvements') }}
                {{ Form::textarea('improvements', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('well', 'What Worked Well?') }}
                {{ Form::textarea('well', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('comments', 'Additional Comments') }}
                {{ Form::textarea('comments', '', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection