@extends('layouts.admin.b4')
@section('content')
<br>
<div class="container">
    <table class="table table-striped table-bordered">
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
                    <td>{{ number_format($journ->amount, 2, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection