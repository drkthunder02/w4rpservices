@extends('layouts.b4')
@section('content')

{!! Form::open(['action' => 'MoonsAdminController@updateMoonPaid', 'method' => 'POST']) !!}
<div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>System</th>
                <th>Name</th>
                <th>Rental Price</th>
                <th>Ally Rental Price</th>
                <th>Renter</th>
                <th>Rental End</th>
                <th>Paid?</th>
            </thead>
            <tbody>
                @foreach($table as $row)
                <tr class="{{ $row['RowColor'] }}">
                    <td>{{ $row['SPM'] }}</td>
                    <td>{{ $row['StructureName'] }}</td>
                    <td>{{ $row['AlliancePrice'] }}</td>
                    <td>{{ $row['OutOfAlliancePrice'] }}</td>
                    <td>{{ $row['Renter'] }}</td>
                    <td>{{ $row['RentalEnd'] }}</td>
                    <td>
                    @if ($row['Paid'] == 'Yes')
                        {{ Form::radio('paid', $row['SPM'], true, ['class' => 'form-control']) }}
                    @else
                        {{ Form::radio('paid', null, false, ['class' => 'form-control']) }}
                    @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
{!! Form::close() !!}
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Legend
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr class="table-primary">
                                <td>Moon Available</td>
                            </tr>
                            <tr class="table-danger">
                                <td>Moon Rented</td>
                            </tr>
                            <tr class="table-warning">
                                <td>Moon Rent Due</td>
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