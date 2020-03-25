@extends('layouts.b4')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="container">
            <h2>Moons in Warped Intentions Sovereignty</h2>
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
                <table class="table table-bordered">
                    <thead>
                        <th>Location</th>
                        <th>Corporation</th>
                        <th>First Ore</th>
                        <th>First Quantity</th>
                        <th>Second Ore</th>
                        <th>Second Quantity</th>
                        <th>Third Ore</th>
                        <th>Third Quantity</th>
                        <th>Fourth Ore</th>
                        <th>Fourth Quantity</th>
                        <th>Availability</th>
                    </thead>
                    <tbody>
            @foreach($moons as $moon)
                @if($moon->System == $system)
                    @if($moon->Availability == 'Deployed')
                    <tr class="table-danger">
                    @else
                    <tr>
                    @endif
                        <td>{{ $moon->System . " - " . $moon->Planet . " - " . $moon->Moon }}</td>
                        <td>{{ $moon->Corporation }}</td>
                        @if(in_array($moon->FirstOre, $gasGoo))
                            <td class="table-secondary">{{ $moon->FirstOre }}</td>
                            <td class="table-secondary">{{ $moon->FirstQuantity }}</td>
                        @elseif(in_array($moon->FirstOre, $r8Goo))
                            <td class="table-primary">{{ $moon->FirstOre }}</td>
                            <td class="table-primary">{{ $moon->FirstQuantity }}</td>
                        @elseif(in_array($moon->FirstOre, $r16Goo))
                            <td class="table-success">{{ $moon->FirstOre }}</td>
                            <td class="table-success">{{ $moon->FirstQuantity }}</td>
                        @elseif(in_array($moon->FirstOre, $r32Goo))
                            <td class="table-warning">{{ $moon->FirstOre }}</td>
                            <td class="table-warning">{{ $moon->FirstQuantity }}</td>
                        @elseif(in_array($moon->FirstOre, $r64Goo))
                            <td class="table-danger">{{ $moon->FirstOre }}</td>
                            <td class="table-danger">{{ $moon->FirstQuantity }}</td>
                        @else
                            <td>{{ $moon->FirstOre }}</td>
                            <td>{{ $moon->FirstQuantity }}</td>
                        @endif
                        @if(in_array($moon->SecondOre, $gasGoo))
                            <td class="table-light">{{ $moon->SecondOre }}</td>
                            <td class="table-light">{{ $moon->SecondQuantity }}</td>
                        @elseif(in_array($moon->SecondOre, $r8Goo))
                            <td class="table-primary">{{ $moon->SecondOre }}</td>
                            <td class="table-primary">{{ $moon->SecondQuantity }}</td>
                        @elseif(in_array($moon->SecondOre, $r16Goo))
                            <td class="table-success">{{ $moon->SecondOre }}</td>
                            <td class="table-success">{{ $moon->SecondQuantity }}</td>
                        @elseif(in_array($moon->SecondOre, $r32Goo))
                            <td class="table-warning">{{ $moon->SecondOre }}</td>
                            <td class="table-warning">{{ $moon->SecondQuantity }}</td>
                        @elseif(in_array($moon->SecondOre, $r64Goo))
                            <td class="table-danger">{{ $moon->SecondOre }}</td>
                            <td class="table-danger">{{ $moon->SecondQuantity }}</td>
                        @else
                            <td>{{ $moon->SecondOre }}</td>
                            <td>{{ $moon->SecondQuantity }}</td>
                        @endif
                        @if(in_array($moon->ThirdOre, $gasGoo))
                            <td class="table-light">{{ $moon->ThirdOre }}</td>
                            <td class="table-light">{{ $moon->ThirdQuantity }}</td>
                        @elseif(in_array($moon->ThirdOre, $r8Goo))
                            <td class="table-primary">{{ $moon->ThirdOre }}</td>
                            <td class="table-primary">{{ $moon->ThirdQuantity }}</td>
                        @elseif(in_array($moon->ThirdOre, $r16Goo))
                            <td class="table-success">{{ $moon->ThirdOre }}</td>
                            <td class="table-success">{{ $moon->ThirdQuantity }}</td>
                        @elseif(in_array($moon->ThirdOre, $r32Goo))
                            <td class="table-warning">{{ $moon->ThirdOre }}</td>
                            <td class="table-warning">{{ $moon->ThirdQuantity }}</td>
                        @elseif(in_array($moon->ThirdOre, $r64Goo))
                            <td class="table-danger">{{ $moon->ThirdOre }}</td>
                            <td class="table-danger">{{ $moon->ThirdQuantity }}</td>
                        @else
                            <td>{{ $moon->ThirdOre }}</td>
                            <td>{{ $moon->ThirdQuantity }}</td>
                        @endif
                        @if(in_array($moon->FourthOre, $gasGoo))
                            <td class="table-light">{{ $moon->FourthOre }}</td>
                            <td class="table-light">{{ $moon->FourthQuantity }}</td>
                        @elseif(in_array($moon->FourthOre, $r8Goo))
                            <td class="table-primary">{{ $moon->FourthOre }}</td>
                            <td class="table-primary">{{ $moon->FourthQuantity }}</td>
                        @elseif(in_array($moon->FourthOre, $r16Goo))
                            <td class="table-success">{{ $moon->FourthOre }}</td>
                            <td class="table-success">{{ $moon->FourthQuantity }}</td>
                        @elseif(in_array($moon->FourthOre, $r32Goo))
                            <td class="table-warning">{{ $moon->FourthOre }}</td>
                            <td class="table-warning">{{ $moon->FourthQuantity }}</td>
                        @elseif(in_array($moon->FourthOre, $r64Goo))
                            <td class="table-danger">{{ $moon->FourthOre }}</td>
                            <td class="table-danger">{{ $moon->FourthQuantity }}</td>
                        @else
                            <td>{{ $moon->FourthOre }}</td>
                            <td>{{ $moon->FourthQuantity }}</td>
                        @endif
                        <td>{{ $moon->Availability }}</td>
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
                            <tr class="table-light">
                                <td>Gas Ore</td>
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