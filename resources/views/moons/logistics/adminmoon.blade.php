@extends('layouts.b4')
@section('content')
<br>
<div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>System</th>
                <th>Name</th>
                <th>Rental End</th>
            </thead>
            <tbody>
                @foreach($table as $row)
                <tr class="{{ $row['RowColor'] }}">
                    <td>{{ $row['SPM'] }}</td>
                    <td>{{ $row['StructureName'] }}</td>
                    <td>{{ $row['RentalEnd'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
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
                            <tr class="table-success">
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