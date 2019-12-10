@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Moon Ledger</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Character</th>
                    <th>Ore Name</th>
                    <th>Quantity</th>
                </thead>
                <tbody>
                    @foreach($mining as $min)
                        <tr>
                            <td>{{ $min['character'] }}</td>
                            <td>{{ $min['ore'] }}</td>
                            <td>{{ $min['quantity'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection