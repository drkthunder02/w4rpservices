@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Financial Outlook for the Alliance</h2>
            All numbers are in millions.
        </div>
        <div class="card-body">
            <div id="finances-div"></div>
            {!! $lava->render('ComboChart', 'Finances', 'finances-div') !!}
        </div>
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Income Streams</h2>
        </div>
        <div class="card-body">
            <div id="income-div"></div>
            {!! $lava->render('PieChart', 'Incomes', 'income-div') !!}
        </div>
    </div>
</div>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Expenses</h2>
        </div>
        <div class="card-body">
            <div id="expense-div"></div>
            {!! $lava->render('PieChart', 'Expenses', 'expense-div') !!}
        </div>
    </div>
</div>
@endsection