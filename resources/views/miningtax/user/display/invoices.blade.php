@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Unpaid Invoices</h2><br>
            <h3>Amount: {!! $unpaidAmount !!}</h3>
        </div>
        <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <th>Invoice Id</th>
                <th>Amount</th>
                <th>Due Date</th>
            </thead>
            <tbody>
            @foreach($unpaid as $un)
                <tr>
                    <td>{{ $un->invoice_id }}</td>
                    <td>{{ $un->amount }}</td>
                    <td>{{ $un->due_date }}</td>
                </tr>
            @endforeach()
            </tbody>
        </table>
        {{ $unpaid->links() }}
        </div>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Paid Invoices</h2><br>
            <h3>Amount: {!! $paidAmount !!}</h3>
        </div>
        <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <th>Invoice Id</th>
                <th>Amount</th>
                <th>Due Date</th>
            </thead>
            <tbody>
                @foreach($paid as $p)
                <tr>
                    <td>{{ $p->invoice_id }}</td>
                    <td>{{ $p->amount }}</td>
                    <td>{{ $p->due_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $paid->links() }}
        </div>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Late Invoices</h2>
        </div>
        <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <th>Invoice Id</th>
                <th>Amount</th>
                <th>Due Date</th>
            </thead>
            <tbody>
            @foreach($late as $l)
            <tr>
                <td>{{ $l->invoice_id }}</td>
                <td>{{ $l->amount }}</td>
                <td>{{ $l->due_date }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $late->links() }}
        </div>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Deferred Invoices</h2>
        </div>
        <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <th>Invoice Id</th>
                <th>Amount</th>
                <th>Due Date</th>
            </thead>
            <tbody>
            @foreach($deferred as $d)
            <tr>
                <td>{{ $d->invoice_id }}</td>
                <td>{{ $d->amount }}</td>
                <td>{{ $d->due_date }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $deferred->links() }}
        </div>
    </div>
</div>
@endsection