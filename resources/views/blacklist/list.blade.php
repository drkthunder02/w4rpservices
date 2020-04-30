@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container col-md-10">
    <div class="card">
        <div class="card-header">
            <h2>Alliance Blacklist</h2>
        </div>
        <div class="card-body">
            @if($blacklist != null)
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Entity Id</th>
                    <th>Entity Name</th>
                    <th>Entity Type
                    <th>Reason</th>
                    <th>Alts</th>
                </thead>
                <tbody>
                    @foreach($blacklist as $bl)
                        <tr>
                            <td>{{ $bl->entity_id }}</td>
                            <td>{{ $bl->entity_name }}</td>
                            <td>{{ $bl->entity_type }}</td>
                            <td>{{ $bl->reason }}</td>
                            @if($bl->alts != null)
                            <td>{{ $bl->alts }}</td>
                            @else
                            <td> </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <b><h3>No Characters Found</h3></b>
            @endif
        </div>
    </div>
</div>
@if($blacklist != null)
{{ $blacklist->links() }}
@endif
@endsection