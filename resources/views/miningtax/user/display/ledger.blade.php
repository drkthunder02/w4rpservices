@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="container">
            <h2>Moon Mining Ledgers</h2>
        </div>
    </div>
    <br>
    <ul class="nav nav-pills">
        @foreach($structures as $structure)
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#W4RP-{{$structure}}">{{$structure}}</a>
        </li>
        @endforeach
    </ul>
    <br>
    <div class="tab-content">
        @foreach($structures as $structure)
        <div id="W4RP-{{$structure}}" class="tab-pane fade">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Structure</th>
                    <th>Character</th>
                    <th>Corp Ticker</th>
                    <th>Ore</th>
                    <th>Quantity</th>
                    <th>Updated</th>
                </thead>
                <tbody>
                    @foreach($miningLedgers as $ledger)
                        @if($ledger['structure'] == $structure['name'])
                            <tr>
                                <td>{{ $ledger['structure'] }}</td>
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