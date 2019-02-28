@extends('layouts.b4')
@section('content')


<div class="row">
    <div class="container">
        <div class="card">
            <div class="card-header">
                PI Taxes
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Month</th>
                        <th>PI Taxes</th>
                    </thead>
                    <tbody>
                        @foreach($pis as $pi)
                            <tr>
                                <td>{{ $pi['date'] }}</td>
                                <td>{{ $pi['gross'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Office Taxes
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Month</th>
                        <th>Office Taxes</th>
                    </thead>
                    <tbody>
                        @foreach($offices as $office)
                            <tr>
                                <td>{{ $office['date'] }}</td>
                                <td>{{ $office['gross'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Industry Taxes
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Month</th>
                        <th>Industry Taxes</th>
                    </thead>
                    <tbody>
                        @foreach($industrys as $industry)
                            <tr>
                                <td>{{ $industry['date'] }}</td>
                                <td>{{ $industry['gross'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Reprocessing Taxes
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Month</th>
                        <th>Reprocessing Taxes</th>
                    </thead>
                    <tbody>
                        @foreach($reprocessings as $reprocessing)
                            <tr>
                                <td>{{ $reprocessing['date'] }}</td>
                                <td>{{ $reprocessing['gross'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="container">
        <div class="card">
            <div class="card-header">
                Market Taxes
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Month</th>
                        <th>Market Taxes</th>
                    </thead>
                    <tbody>
                        @foreach($markets as $market)
                            <tr>
                                <td>{{ $market['date'] }}</td>
                                <td>{{ $market['gross'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Jump Gate Taxes
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Month</th>
                        <th>Jump Gate Taxes</th>
                    </thead>
                    <tbody>
                        @foreach($jumpgates as $jumpgate)
                            <tr>
                                <td>{{ $jumpgate['date'] }}</td>
                                <td>{{ $jumpgate['gross'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection