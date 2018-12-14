@extends('layouts.b4')
@section('content')

<div class="container col-md-12">
    <table class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach($journal as $journ)
                <tr>
                    <td>{{ $journ->first_party_id }}</td>
                    <td>{{ $journ->date }}</td>
                    <td>{{ $journ->description }}</td>
                    <td>{{ $journ->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection