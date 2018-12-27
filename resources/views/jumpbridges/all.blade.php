@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h2>Overall Jump Bridge Usage</h2>
            </div>
            <div class="card-body">
                Last 30 Days: {{ $data['30days'] }}<br>
                Last 60 Days: {{ $data{'60days'} }}<br>
                Last 90 Days: {{ $data['90days'] }}<br>
            </div>
        </div>
    </div>
</div>
@endsection