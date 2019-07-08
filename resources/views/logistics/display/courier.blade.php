@extends('layouts.b4')
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Courier Contract Details</h2>
    </div>
    <div class="card-body">
        Start System: {{ $startSystem }}<br>
        End System:  {{ $endSystem }}<br>
        Contract To: {{ $corporation }}<br>
        Reward:  {{ number_format($reward, 2, '.', ',') }}<br>
        Collateral: {{ number_format($collateral, 2, '.', ',') }}<br>
        Expiration: 7 days<br>
        Days To Complete: 7 days<br>
    </div>
</div>
@endsection