@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row">
        <p align="center"><h2>Structure Dashboard</h2></p>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-6 card">
            <div class="card-header">
                Add Tax Ratio for Structure
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'Structures\StructureAdminController@storeTaxRatio', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('corpId', 'Corporation ID:') }}
                    {{ Form::text('corpId', '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('corporation', 'Corporation Name') }}
                    {{ Form::text('corporation', '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('type', 'Structure Type') }}
                    {{ Form::select('type', ['Refinery' => 'Refinery', 'Market' => 'Market'], null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('ratio', 'Tax Ratio') }}
                    {{ Form::text('ratio', '', ['class' => 'form-control']) }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-6 card">
            <div class="card-header">
                Update Tax Ratio for Structure
            </div>
            <div class="card-body">
                {!! Form::open(['action' => 'Structures\StructureAdminController@updateTaxRatio', 'method' => 'POST']) !!}
                <div class="form-group">
                    {{ Form::label('corporation', 'Corporation Name') }}
                    {{ Form::text('corporation', '', ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('type', 'Structure Type') }}
                    {{ Form::select('type', ['Refinery' => 'Refinery', 'Market' => 'Market'], null, ['class' => 'form-control']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('ratio', 'Tax Ratio') }}
                    {{ Form::text('ratio', '', ['class' => 'form-control']) }}
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection