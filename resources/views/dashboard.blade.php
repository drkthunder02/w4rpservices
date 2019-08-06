@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Dashboard
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Welcome to the dashboard
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Open SRP Requests<br>
                            # Open: {{ $openCount }}<br>
                        </div>
                        <div class="col">
                            <div id="under-review-div"></div>
                            {!! $lava->render('GaugeChart', 'SRP', 'under-review-div') !!}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($openCount > 0)
                    <table class="table table-striped">
                        <thead>
                            <th>Character</th>
                            <th>Fleet Commander</th>
                            <th>Ship Type</th>
                            <th>Loss Value</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach($open as $o)
                            <tr>
                                <td>{{ $o['character_name'] }}</td>
                                <td>{{ $o['fleet_commander_name'] }}</td>
                                <td>{{ $o['ship_type'] }}</td>
                                <td>{{ $o['loss_value'] }}</td>
                                <td>{{ $o['approved'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    No Open SRP Requests
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Denied SRP Requests<br>
                    # Denied: {{ $deniedCount }}
                </div>
                <div class="card-body">
                    @if($deniedCount > 0)
                    <table class="table table-striped">
                        <thead>
                            <th>Character</th>
                            <th>Fleet Commander</th>
                            <th>Ship Type</th>
                            <th>Loss Value</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </thead>
                        <tbody>
                            @foreach($denied as $d)
                            <tr>
                                <td>{{ $d['character_name'] }}</th>
                                <td>{{ $d['fleet_commander_name'] }}</td>
                                <td>{{ $d['ship_type'] }}</td>
                                <td>{{ $d['loss_value'] }}</td>
                                <td>{{ $d['approved'] }}</td>
                                <td>{{ $d['notes'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    No Denied SRP Requests
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Approved SRP Requests<br>
                    # Approved: {{ $approvedCount }}
                </div>
                <div class="card-body">
                    @if($approvedCount > 0)
                    <table class="table table-striped">
                        <thead>
                            <th>Character</th>
                            <th>Fleet Commander</th>
                            <th>Ship Type</th>
                            <th>Loss Value</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach($approved as $a)
                            <tr>
                                <td>{{ $a['character_name'] }}</td>
                                <td>{{ $a['fleet_commander_name'] }}</td>
                                <td>{{ $a['ship_type'] }}</td>
                                <td>{{ $a['loss_value'] }}</td>
                                <td>{{ $a['approved'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    No Approved SRP Requests
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
