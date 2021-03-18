@extends('layouts.admin.b4')
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
                        <td>{{ number_format($invoice->invoice_amount, 2, ".", ",") }}</td>
                        <td>{{ $invoice->date_issued }}</td>
                        <td>{{ $invoice->date_due }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $invoices->links() }}
            <h2>Total Paid: {{ number_format($totalAmount, 2, ".", ",") }}</h2>
        </div>
    </div>
</div>
@endsection