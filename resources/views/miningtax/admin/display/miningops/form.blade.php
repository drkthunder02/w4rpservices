@extends('layouts.admin.b4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Alliance Mining Operations Form</h2>
            <h4>Enter in the information, then hit submit.</h4>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'MiningTaxes\MiningTaxesAdminController@storeMiningOperationForm', 'method' => 'POST']) !!}
            <div class="form-group col-md-6">
                {{ Form::label('name', 'Mining Operation Name') }}
                {{ Form::text('name', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('date', 'Date') }}
                {{ Form::date('date', \Carbon\Carbon::now(), ['class' => 'form-control']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('structure', 'Mining Structure') }}
                {{ Form::select('structure', $structures, 'None', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Current Alliance Mining Operations</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Operation Date</th>
                    <th>Operation Name</th>
                    <th>Structure Name</th>
                    <th>Authorized By</th>
                </thead>
                <tbody>
                    @if($operations != null)
                    @foreach($operations as $operation)
                    <tr>
                        <td>{{ $operation->operation_date }}</td>
                        <td>{{ $operation->operation_name }}</td>
                        <td>{{ $operation->structure_name }}</td>
                        <td>{{ $operation->authorized_by_name }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection