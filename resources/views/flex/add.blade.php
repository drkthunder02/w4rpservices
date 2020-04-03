@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>New Flex Structure Registration</h2>
        </div>
        <div class="card-body">
            {{ Form::open(['action' => 'Flex\FlexAdminController@addFlexStructure', 'method' => 'POST']) }}
            <div class="form-group">
                {{ Form::label('requestor_name', 'Character Name') }}
                {{ Form::text('requestor_name', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('requestor_corp_name', 'Corporation Name') }}
                {{ Form::text('requestor_corp_name', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('system', 'System') }}
                {{ Form::text('system', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('structure_type', 'Structure Type') }}
                {{ Form::select('structure_type', [
                    'Cyno Jammer' => 'Cyno Jammer',
                    'Cyno Beacon' => 'Cyno Beacon',
                    'Jump Bridge' => 'Jump Bridge',
                    'Super Construction Facilities' => 'Super Construction Facilities',
                ], 'None', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('structure_cost', 'Structure Cost') }}
                {{ Form::text('structure_cost', '', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection