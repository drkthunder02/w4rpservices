@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <table class="table table-striped table-bordered">
        <thead>
            <th>Observer Id</th>
            <th>Character Id</th>
            <th>Last Updated</th>
            <th>Type Id</th>
            <th>Quantity</th>
        </thead>
        <tbody>
            @foreach($ledgers as $ledger)
            <tr>
                <td>{{ $ledger['observer_id'] }}</td>
                <td>{{ $ledger['character_id'] }}</td>
                <td>{{ $ledger['last_updated'] }}</td>
                <td>{{ $ledger['type_id'] }}</td>
                <td>{{ $ledger['quantity'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection