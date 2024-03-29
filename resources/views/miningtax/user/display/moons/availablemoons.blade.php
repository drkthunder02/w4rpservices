@extends('layouts.user.dashb4')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="container">
            <h2>Available Moons for Rent in Warped Intentions Sovreignty</h2>
        </div>
    </div>
    <br>
    <ul class="nav nav-pills">
        @foreach($systems as $system)
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#W4RP-{{$system}}">{{$system}}</a>
        </li>
        @endforeach
    </ul>
    <br>
    <div class="tab-content">
        @foreach($systems as $system)
        <div id="W4RP-{{ $system }}" class="tab-pane fade">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Location</th>
                    <th>First Ore</th>
                    <th>First Quantity</th>
                    <th>Second Ore</th>
                    <th>Second Quantity</th>
                    <th>Third Ore</th>
                    <th>Third Quantity</th>
                    <th>Fourth Ore</th>
                    <th>Fourth Quantity</th>
                    <th>Moon Worth</th>
                    <th>Rental Cost</th>
                    <th>Rent?</th>
                </thead>
                <tbody>
                @foreach($moons as $moon)
                    @if($moon['system'] == $system)
                    <tr>
                        <td>{{ $moon['moon_name'] }}</td>
                    @if(isset($moon['ores'][0]))
                        @if(in_array($moon['ores'][0]['ore_name'], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-secondary">{{ number_format(($moon['ores'][0]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][0]['ore_name'], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-primary">{{ number_format(($moon['ores'][0]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][0]['ore_name'], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-success">{{ number_format(($moon['ores'][0]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][0]['ore_name'], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-warning">{{ number_format(($moon['ores'][0]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][0]['ore_name'], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-danger">{{ number_format(($moon['ores'][0]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @if(isset($moon['ores'][1]))
                        @if(in_array($moon['ores'][1]['ore_name'], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-secondary">{{ number_format(($moon['ores'][1]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][1]['ore_name'], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-primary">{{ number_format(($moon['ores'][1]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][1]['ore_name'], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-success">{{ number_format(($moon['ores'][1]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][1]['ore_name'], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-warning">{{ number_format(($moon['ores'][1]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][1]['ore_name'], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-danger">{{ number_format(($moon['ores'][1]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @if(isset($moon['ores'][2]))
                        @if(in_array($moon['ores'][2]['ore_name'], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-secondary">{{ number_format(($moon['ores'][2]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][2]['ore_name'], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-primary">{{ number_format(($moon['ores'][2]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][2]['ore_name'], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-success">{{ number_format(($moon['ores'][2]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][2]['ore_name'], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-warning">{{ number_format(($moon['ores'][2]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][2]['ore_name'], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-danger">{{ number_format(($moon['ores'][2]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @if(isset($moon['ores'][3]))
                        @if(in_array($moon['ores'][3]['ore_name'], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-secondary">{{ number_format(($moon['ores'][3]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][3]['ore_name'], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-primary">{{ number_format(($moon['ores'][3]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][3]['ore_name'], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-success">{{ number_format(($moon['ores'][3]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][3]['ore_name'], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-warning">{{ number_format(($moon['ores'][3]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                        @if(in_array($moon['ores'][3]['ore_name'], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-danger">{{ number_format(($moon['ores'][3]['quantity'] * 100.00), 2, ".", ",") }}%</td>
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    <td>{{ number_format($moon['worth_amount'], 0, ".", ",") }}</td>
                    <td>{{ number_format($moon['rental_amount'], 0, ".", ",") }}</td>
                    <td>
                        {!! Form::open(['action' => 'MiningTaxes\MiningTaxesController@DisplayMoonRentalForm', 'method' => 'POST']) !!}
                        {{ Form::hidden('moon_id', $moon['moon_id']) }}
                        {{ Form::hidden('moon_name', $moon['moon_name']) }}
                        {{ Form::hidden('worth_amount', $moon['worth_amount']) }}
                        {{ Form::hidden('rental_amount', $moon['rental_amount']) }}
                        {{ Form::submit('Rent', ['class' => 'btn btn-danger']) }}
                        {!! Form::close() !!}
                    </td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
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
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr class="table-secondary">
                                <td>R4 Ore</td>
                            </tr>
                            <tr class="table-primary">
                                <td>R8 Ore</td>
                            </tr>
                            <tr class="table-success">
                                <td>R16 Ore</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Moon Rental Notes
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr><td>1. Moon Rentals are for a period of 3 months.</td></tr>
                            <tr><td>2. Moon Rental amount already accounts for the period of 3 months.</td></tr>
                            <tr><td>3. Payment must be made before structure is placed down, no exceptions.</td></tr>
                            <tr><td>4. You are responsible for your own structure and fueling of your own structure.</td></tr>
                            <tr><td>5. No reprocessing modules are allowed for moon rental structures.</td></tr>
                            <tr><td>6. There is no maximum time on moon rentals.</td></tr>
                            <tr><td>7. Minimum time of rental is 3 months.</td></tr>
                            <tr><td>8. Billing cycle is every 3 months.  Rentals are in multiples of 3 months.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col"></div>
        <div class="col"></div>
    </div>
</div>
@endsection