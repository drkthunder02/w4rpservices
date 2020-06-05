@extends('layouts.admin.b4')
@section('content')
<br>
{!! Form::open(['action' => 'Moons\MoonsAdminController@storeMoonRemoval', 'method' => 'POST']) !!}
<div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <th>System</th>
                <th>Name</th>
                <th>Rental Price</th>
                <th>Ally Rental Price</th>
                <th>Renter</th>
                <th>Contact</th>
                <th>Rental End</th>
                <th>Paid?</th>
                <th>Paid Until</th>
                <th>Remove Renter</th>
            </thead>
            <tbody>
                @foreach($table as $row)
                <tr class="{{ $row['RowColor'] }}">
                    <td>{{ $row['SPM'] }}</td>
                    <td>{{ $row['StructureName'] }}</td>
                    <td>{{ number_format($row['AlliancePrice'], 0, ".", ",") }}</td>
                    <td>{{ number_format($row['OutOfAlliancePrice'], 0, ".", ",") }}</td>
                    <td>{{ $row['Renter'] }}</td>
                    <td>{{ $row['Contact'] }}</td>
                    <td>{{ $row['RentalEnd'] }}</td>
                    @if($row['Paid'] == 'Yes')
                        <td>Yes</td>
                    @else
                        <td>No</td>
                    @endif
                    <td>{{ $row['PaidUntil'] }}</td>
                    <td>{{ Form::radio('remove', $row['SPM'], false, ['class' => 'form-control']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
{!! Form::close() !!}
<br>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Legend
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr class="table-success">
                                <td>Moon Available</td>
                            </tr>
                            <tr class="table-danger">
                                <td>Moon Rented</td>
                            </tr>
                            <tr class="table-warning">
                                <td>Moon Rent Due</td>
                            </tr>
                            <tr class="table-info">
                                <td>Alliance Use</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
        <div class="col"></div>
    </div>
</div>


@endsection