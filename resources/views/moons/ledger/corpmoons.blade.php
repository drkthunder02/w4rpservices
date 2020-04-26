@extends('layouts.b4')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="container">
            <h2>Moon Ledger</h2>
        </div>
    </div>
    <br>
    <ul class="nav nav-pills">
        @foreach($structures as $key => $value)
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#W4RP-{{$value}}">{{$value}}</a>
        </li>
        @endforeach
    </ul>
    <br>
    <div class="tab-content">
        @foreach($structures as $key => $value)
            <div id="W4RP-{{ $value }}" class="tab-pane fade">
                <table class="table table-bordered">
                    <thead>
                        <th>Character</th>
                        <th>Corp Ticker</th>
                        <th>Ore Name</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </thead>
                    <tbody>
                        @foreach($miningLedgers as $ledger)
                            @if($ledger['structure'] == $value)
                            <tr>
                                <td>{{ $ledger['character'] }}</td>
                                <td>{{ $ledger['corpTicker'] }}</td>
                                <td>{{ $ledger['ore'] }}</td>
                                <td>{{ $ledger['quantity'] }}</td>
                                <td>{{ $ledger['updated'] }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>
@endsection