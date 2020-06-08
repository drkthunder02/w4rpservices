@extends('layous.admin.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Set Rental Moons for Alliance</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'Moons\MoonsAdminController@storeRentalMoonForAlliance', 'method' => 'POST']) !!}
            <div class="form-group">
                {{ Form::label('moon', 'Moon') }}
                {{ Form::select('moon', $moons, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('until', 'End Date') }}
                {{ Form::date('until', \Carbon\Carbon::now()->endOfMonth(), ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection