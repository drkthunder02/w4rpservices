@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Contract Dashboard</h2>
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <a href="/contracts/admin/new" class="btn btn-primary" role="button">Create New Contract</a>
    </div>
</div>
<br>
@if(count($contracts))
@foreach($contracts as $contract)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm" align="left">
                            {{ $contract['title'] }}
                        </div>
                        <div class="col-sm" align="center">
                            Type: {{ $contract['type'] }}
                        </div>
                        <div class="col-sm" align="right">
                            <a href="/contracts/admin/delete/{{ $contract['contract_id'] }}" class="btn btn-primary" role="button">Delete Contract</a>
                            <br><br>
                            <a href="/contracts/admin/end/{{ $contract['contract_id'] }}" class="btn btn-primary" role="button">End Contract</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        End Date: {{ $contract['end_date'] }}
                    </div>
                    <span class="border-dark">
                        <div class="container">
                            {!! $contract['body'] !!}
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endforeach
@else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    No Contracts Issued
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection