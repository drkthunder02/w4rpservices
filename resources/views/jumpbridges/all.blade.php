@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row">
        <div class="card col-md-6">
            <div class="card-header">
                <h2>Overall Jump Bridge Usage</h2>
            </div>
            <div class="card-body">
                Last 30 Days: {{ $data['30days'] }}
                Last 60 Days: {{ $data{'60days'} }}
                Last 90 Days: {{ $data['90days'] }}
            </div>
        </div>
    </div>
</div>
@endsection