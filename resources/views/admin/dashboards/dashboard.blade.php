@extends('layouts.admin.b4')
@section('content')
<div class="container col-md-12">
    <div class="card">
        <div class="card-header">
            <h2>Admin Dashboard</h2>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div id="income-div"></div>
                {!! $lava->render('PieChart', 'Income', 'income-div') !!}
            </div>
        </div>
    </div>
</div>
@endsection