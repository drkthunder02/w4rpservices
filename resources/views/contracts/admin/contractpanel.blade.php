@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Test Dashboard</h2>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    New Contracts
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <th>Number</th>
                            <th>Name</th>
                            <th>Items</th>
                            <th>End Date</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Name</td>
                                <td>2 Nags</td>
                                <td>24-04-2019</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container col-md-12">
    <div class="card">
        <div class="card-header">
            Current Top Bid
        </div>
        <div class="card-body">
            Some corporation and the price
        </div>
    </div>
</div>
@endsection