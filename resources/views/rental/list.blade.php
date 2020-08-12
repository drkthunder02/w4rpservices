@extends('layouts.admin.b4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Rental Systems</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>Contact</th>
                    <th>Corporation</th>
                    <th>System</th>
                    <th>Cost</th>
                    <th>Paid Until</th>
                    <th>Update</th>
                    <th>Remove?</th>
                    <th> </th>
                </thead>
                <tbody>
                    @foreach($rentals as $rental)
                    <tr>
                        <td>{{ $rental->contact_name }}</td>
                        <td>{{ $rental->corporation_name }}</td>
                        <td>{{ $rental->system_name }}</td>
                        <td>{{ number_format($rental->rental_cost, 0, ".", ",") }}</td>
                        <td>{{ date_format($rental->paid_until, "Y-m-d") }}</td>
                        <td>
                            {!! Form::open(['action' => 'SystemRentals\RentalAdminController@updateRentalSystem', 'method' => 'POST']) !!}
                            {{ Form::date('paid_until', \Carbon\Carbon::now()->endOfMonth(), ['class' => 'form-control']) }}
                            {{ Form::hidden('contact_id', $rental->contact_id) }}
                            {{ Form::hidden('corporation_id', $rental->corporation_id) }}
                            {{ Form::hidden('system_id', $rental->system_id) }}
                            {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
                            {!! Form::close() !!}
                        </td>
                        <td>
                            {!! Form::open(['action' => 'SystemRentals\RentalAdminController@removeRentalSystem', 'method' => 'POST']) !!}
                            {{ Form::radio('remove', 'Yes', false, ['class' => 'form-control']) }}
                            {{ Form::hidden('contact_id', $rental->contact_id) }}
                            {{ Form::hidden('corporation_id', $rental->corporation_id) }}
                            {{ Form::hidden('system_id', $rental->system_id) }}
                        </td>
                        <td>
                            {{ Form::submit('Remove', ['class' => 'btn btn-danger']) }}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection