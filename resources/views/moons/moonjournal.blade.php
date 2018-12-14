@extends('layouts.b4')
@section('content')

<div class="container col-md-12">
    {{ var_dump($journal) }}
    <table class="table table-striped">
        <thead>
            <th>Date</th>
            <th>Description</th>
            <th>Reason</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach($journal as $journ)
                <tr>
                    <td>{{ $journ->date }}</td>
                    <td>{{ $journ->description }}</td>
                    <td>{{ $journ->reason }}</td>
                    <td>{{ $journ->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection