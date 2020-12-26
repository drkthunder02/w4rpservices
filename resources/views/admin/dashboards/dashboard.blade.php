@extends('layouts.admin.b4')
@section('content')
<div class="container col-md-12">
    <div class="card">
        <div class="card-header">
            <h2>Admin Dashboard</h2>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                
            </div>
        </div>
    </div>

    <div id="income-div"></div>
    @piechart('Income', 'income-div')
</div>
@endsection