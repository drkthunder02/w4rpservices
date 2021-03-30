@extends('layouts.admin.b4')
@section('content')
<br>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2>Invoice Status</h2>
        </div>
        <div class="card-body">
            {{ $invoices->links() }}
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Character</th>
                    <th>Invoice Id</th>
                    <th>Amount</th>
                    <th>Date Issued</th>
                    <th>Date Due</th>
                    <th>Status</th>
                    <th>Update</th>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->character_name }}</td>
                        <td>{{ $invoice->invoice_id }}</td>
                        <td>{{ number_format($invoice->invoice_amount, 2, ".", ",") }}</td>
                        <td>{{ $invoice->date_issued }}</td>
                        <td>{{ $invoice->date_due }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>
                            {!! Form::open(['action' => 'MiningTaxes\MiningTaxesAdminController@UpdateInvoice', 'method' => 'POST']) !!}
                            {{ Form::hidden('invoiceId', $invoice->invoice_id) }}
                            {{ Form::label('status', 'Paid') }}
                            {{ Form::radio('status', 'Paid', true) }}
                            {{ Form::label('status', 'Deferred') }}
                            {{ Form::radio('status', 'Deferred') }}
                            {{ Form::label('status', 'Delete') }}
                            {{ Form::radio('status', 'Deleted') }}
                            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <h2>Total Amount Owed: {{ number_format($totalAmount, 2, ".", ",") }}</h2>
        </div>
    </div>
</div>
@endsection