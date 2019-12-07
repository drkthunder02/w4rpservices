@extends('layouts.b4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Flex Structures</h2>
        </div>
        <div class="card-body">
            @if($structures != null)
            {!! Form::open(['action' => 'Flex\FlexAdminController@removeFlexStructure', 'method' => 'POST']) !!}
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Requestor</th>
                    <th>Corp</th>
                    <th>System</th>
                    <th>Structure Type</th>
                    <th>Cost</th>
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
                            {{ Form::radio('remove', 'Yes', false, ['class' => 'form-control']) }}
                            {{ Form::hidden('requestor_name', $structure->requestor_name) }}
                            {{ Form::hidden('system', $structure->system) }}
                            {{ Form::hidden('structure_type', $structure->structure_type) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <h3>No Flex Structures Registered</h3>
            @endif
        </div>
    </div>
</div>
@endsection