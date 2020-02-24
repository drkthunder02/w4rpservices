@extends('layouts.moons.b4')
@section('content')
<div class="container">
<div class="row">
    <h2>Moons in W4RP Space</h2>
    <ul class="nav nav-tabs">
        @foreach($systems as $system)
        <li>
            <a data-toggle="tab" href="#W4RP-{{$system}}">{{$system}}</a>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($systems as $system)
            <div id="W4RP-{{ $system }}" class="tab-pane fade">
                <table class="table table-striped table-bordered">
                    <thead>
                        <th>Location</th>
                        <th>Corporation</th>
                        <th>Structure Name</th>
                        <th>First Ore</th>
                        <th>First Quantity</th>
                        <th>Second Ore</th>
                        <th>Second Quantity</th>
                        <th>Third Ore</th>
                        <th>Third Quantity</th>
                        <th>Fourth Ore</th>
                        <th>Fourth Quantity</th>
                        <th>Available</th>
                    </thead>
                    <tbody>
            @foreach($moons as $moon)
                @if($moon->System == $system)
                    <tr>
                        <td>{{ $moon->System . " - " . $moon->Planet . " - " . $moon->Moon }}</td>
                        <td>{{ $moon->Corporation }}</td>
                        <td>{{ $moon->StructureName }}</td>
                        <td>{{ $moon->FirstOre }}</td>
                        <td>{{ $moon->FirstQuantity }}</td>
                        <td>{{ $moon->SecondOre }}</td>
                        <td>{{ $moon->SecondQuantity }}</td>
                        <td>{{ $moon->ThirdOre }}</td>
                        <td>{{ $moon->ThirdQuantity }}</td>
                        <td>{{ $moon->FourthOre }}</td>
                        <td>{{ $moon->FourthQuantity }}</td>
                        @if($moon->Available == 1)
                        <td>Yes</td>
                        @else
                        <td>No</td>
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
</div>
@endsection