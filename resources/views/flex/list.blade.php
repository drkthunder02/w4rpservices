@extends('layouts.b4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Flex Structures</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Requestor</th>
                    <th>Corp</th>
                    <th>System</th>
                    <th>Structure Type</th>
                    <th>Cost</th>
                    <th>Update</th>
                    <th>Remove?</th>
                </thead>
                <tbody>
                    @foreach($structures as $structure)
                    <tr>
                        <td>{{ $structure->requestor_name }}</td>
                        <td>{{ $structure->requestor_corp_name }}</td>
                        <td>{{ $structure->system }}</td>
                        <td>{{ $structure->structure_type }}</td>
                        <td>{{ number_format($structure->structure_cost, "2", ".", ",") }}</td>
                        <td>
                            {!! Form::open(['action' => 'Flex\FlexAdminController@updateFlexStructure', 'method' => 'POST']) !!}
                            {{ Form::date('paid_until', \Carbon\Carbon::now()->endOfMonth(), ['class' => 'form-control']) }}
                            {{ submit('Update', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </td>
                        <td>
                            {!! Form::open(['action' => 'Flex\FlexAdminController@removeFlexStructure', 'method' => 'POST']) !!}
                            {{ Form::radio('remove', 'Yes', false, ['class' => 'form-control']) }}
                            {{ Form::hidden('structure_type', $structure->structure_type) }}
                            {{ Form::hidden('requestor_id', $structure->requestor_id) }}
                            {{ Form::hidden('requestor_corp_id', $structure->requestor_corp_id) }}
                            {{ Form::hidden('system_id', $structure->system_id) }}
                            {{ Form::submit('Remove', ['class' => 'btn btn-danger']) }}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection