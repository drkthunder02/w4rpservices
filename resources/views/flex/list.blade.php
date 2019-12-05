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
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Requestor</th>
                    <th>Corp</th>
                    <th>System</th>
                    <th>Structure Type</th>
                    <th>Cost</th>
                </thead>
                <tbody>
                    @foreach($structures as $structure)
                    <tr>
                        <td>{{ $structure->requestor_name }}</td>
                        <td>{{ $structure->requestor_corp_name }}</td>
                        <td>{{ $structure->system }}</td>
                        <td>{{ $structure->structure_type }}</td>
                        <td>{{ number_format($structure->structure_cost, "2", ".", ",") }}
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