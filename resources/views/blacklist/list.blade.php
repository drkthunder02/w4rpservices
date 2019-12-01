@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Alliance Blacklist</h2>
        </div>
        <div class="card-body">
            @if($blacklist != null)
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Character Id</th>
                    <th>Character Name</th>
                    <th>Reason</th>
                </thead>
                <tbody>
                    @foreach($blacklist as $bl)
                        <tr>
                            <td>{{ $bl->characer_id }}</td>
                            <td>{{ $bl->name }}</td>
                            <td>{{ $bl->reason }}</td>
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