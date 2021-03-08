@extends('layouts.admin.b4')
@section('content')
<br>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2>Invoice Status</h2>
        </div>
        <div class="card-body">
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
                        <td>{{ $invoice->amount }}</td>
                        <td>{{ $invoice->date_issued }}</td>
                        <td>{{ $invoice->date_due }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>
                            {!! Form::open(['action' => 'MiningTaxes\MiningTaxesAdminController@UpdateInvoice', 'method' => 'POST']) !!}
                            {{ Form::hidden('invoiceId', $invoice->invoice_id) }}
                            {{ Form::label('status', 'Paid') }}
                            {{ Form::radio('status', 'Paid', true, ['class' => 'form-control']) }}
                            {{ Form::label('status', 'Deferred') }}
                            {{ Form::radio('status', 'Deferred', ['class' => 'form-control']) }}
                            {{ Form::label('status', 'Delete') }}
                            {{ Form::radio('status', 'Deleted', ['class' => 'form-control']) }}
                            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection