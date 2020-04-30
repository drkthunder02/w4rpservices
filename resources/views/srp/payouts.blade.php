@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>SRP Payout</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Cost Code</th>
                        <th>Description</th>
                        <th>Payout %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payouts as $payout)
                    <tr>
                        <td>{{ $payout['code'] }}</td>
                        <td>{{ $payout['description'] }}</td>
                        <td>{{ $payout['payout'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection