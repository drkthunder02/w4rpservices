@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Mining Calendar</h2>
        </div>
        <div class="card-body">
            @calendarchart('Extractions', 'extractions_div')
        </div>
    </div>
</div>
@endsection