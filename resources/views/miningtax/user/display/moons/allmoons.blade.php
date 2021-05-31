@extends('layouts.user.dashb4')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="container">
            <h2>Moons in Warped Intentions Sovreignty</h2>
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
                </thead>
                <tbody>
                @foreach($moons as $moon)
                    @if($moon['system'] == $system)
                    <tr>
                    @if(isset($moon['ores'][0]))
                        @if(in_array($moon['ores'][0], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-secondary">{{ round(($moon['ores'][0]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][0], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-primary">{{ round(($moon['ores'][0]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][0], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-success">{{ round(($moon['ores'][0]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][0], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-warning">{{ round(($moon['ores'][0]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][0], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][0]['ore_name'] }}</td>
                        <td class="table-danger">{{ round(($moon['ores'][0]['quantity'] * 100.0), 2) }}
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @if(isset($moon['ores'][1]))
                        @if(in_array($moon['ores'][1], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-secondary">{{ round(($moon['ores'][1]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][1], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-primary">{{ round(($moon['ores'][1]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][1], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-success">{{ round(($moon['ores'][1]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][1], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-warning">{{ round(($moon['ores'][1]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][1], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][1]['ore_name'] }}</td>
                        <td class="table-danger">{{ round(($moon['ores'][1]['quantity'] * 100.0), 2) }}
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @if(isset($moon['ores'][2]))
                        @if(in_array($moon['ores'][2], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-secondary">{{ round(($moon['ores'][2]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][2], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-primary">{{ round(($moon['ores'][2]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][2], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-success">{{ round(($moon['ores'][2]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][2], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-warning">{{ round(($moon['ores'][2]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][2], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][2]['ore_name'] }}</td>
                        <td class="table-danger">{{ round(($moon['ores'][2]['quantity'] * 100.0), 2) }}
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @if(isset($moon['ores'][3]))
                        @if(in_array($moon['ores'][3], $r4Goo))
                        <td class="table-secondary">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-secondary">{{ round(($moon['ores'][3]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][3], $r8Goo))
                        <td class="table-primary">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-primary">{{ round(($moon['ores'][3]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][3], $r16Goo))
                        <td class="table-success">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-success">{{ round(($moon['ores'][3]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][3], $r32Goo))
                        <td class="table-warning">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-warning">{{ round(($moon['ores'][3]['quantity'] * 100.0), 2) }}
                        @endif
                        @if(in_array($moon['ores'][3], $r64Goo))
                        <td class="table-danger">{{ $moon['ores'][3]['ore_name'] }}</td>
                        <td class="table-danger">{{ round(($moon['ores'][3]['quantity'] * 100.0), 2) }}
                        @endif
                    @else
                    <td></td>
                    <td></td>
                    @endif
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
                    <table class="table table-striped">
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
                            <tr class="table-warning">
                                <td>R32 Ore</td>
                            </tr>
                            <tr class="table-danger">
                                <td>R64 Ore</td>
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