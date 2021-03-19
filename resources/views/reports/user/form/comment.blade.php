@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Add New Comment for After Action Report</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'AfterActionReports\AfterActionReportsController@StoreComment', 'method' => 'POST']) !!}
            {{ Form::hidden('reportId', $id) }}
            <div class="form-group">
                {{ Form::label('comments', 'Comments') }}
                {{ Form::textarea('comments', '', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection