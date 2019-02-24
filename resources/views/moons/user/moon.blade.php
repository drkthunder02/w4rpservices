@extends('layouts.b4')
@section('content')
<div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>System</th>
                <th>Name</th>
                <th>First Ore</th>
                <th>Quantity</th>
                <th>Second Ore</th>
                <th>Quantity</th>
                <th>Third Ore</th>
                <th>Quantity</th>
                <th>Fourth Ore</th>
                <th>Quantity</th>
                <th>Rental Price</th>
                <th>Moon Worth</th>
                <th>Rental End</th>
            </thead>
            <tbody>
                @foreach($table as $row)
                <tr>
                    <td class="{{ $row['RowColor'] }}">{{ $row['SPM'] }}</td>
                    <td class="{{ $row['RowColor'] }}">{{ $row['StructureName'] }}</td>
                    <td>{{ $row['FirstOre'] }}</td>
                    <td>{{ $row['FirstQuantity'] }}</td>
                    <td>{{ $row['SecondOre'] }}</td>
                    <td>{{ $row['SecondQuantity'] }}</td>
                    <td>{{ $row['ThirdOre'] }}</td>
                    <td>{{ $row['ThirdQuantity'] }}</td>
                    <td>{{ $row['FourthOre'] }}</td>
                    <td>{{ $row['FourthQuantity'] }}</td>
                    <td>{{ $row['Price'] }}</td>
                    <td>{{ $row['Worth'] }}</td>
                    <td>{{ $row['RentalEnd'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection