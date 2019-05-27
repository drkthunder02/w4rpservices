@extends('layouts.b4')
@section('content')
<div class="container">

<div class="container">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h2>Station Name</h2><br>
                <h3>Date Updated</h3>
            </div>
            <div class="card-body">
                <div table="table table-striped table-bordered">
                    <thead>
                        <th>Location</th>
                        <th>Items</th>
                        <th>Quantities</th>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td></td>
                                <td>Item Name</td>
                                <td>Quantity</td>
                            </tr>
                        @endforeach
                    </tbody>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection