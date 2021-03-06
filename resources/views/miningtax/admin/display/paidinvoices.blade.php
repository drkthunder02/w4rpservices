@extends('layouts.admin.dashb4')
@section('content')
<br>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2>Paid Invoices</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Character</th>
                    <th>Invoice Id</th>
                    <th>Amount</th>
                    <th>Date Issued</th>
                    <th>Date Due</th>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->character_name }}</td>
                        <td>{{ $invoice->invoice_id }}</td>
                        <td>{{ $invoice->amount }}</td>
                        <td>{{ $invoice->date_issued }}</td>
                        <td>{{ $invoice->date_due }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection