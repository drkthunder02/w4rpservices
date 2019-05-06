@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Pick the  Corporation</h2>
    {!! Form::open(['action' => 'Structures\StructureController@displayCorpTaxes', 'method' => 'GET']) !!}
    <div class="form-group col-md-4">
        {{ Form::label('corpId', 'Corporation') }}
        {{ Form::select('corpId', $corps, null, ['class' => 'form-control']) }}
    </div>
    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection