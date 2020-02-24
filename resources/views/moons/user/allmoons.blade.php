@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Moons in W4RP Space</h2>
    <ul class="nav nav-pills">
        @foreach($systems as $system)
        <li><a data-toggle="pill" href="{{ $system }}">{{ $system }}</a></li>
        @endforeach
        <li class="active"><a data-toggle="pill" href="#6X7-JO">6X7-JO</a></li>
        <li><a data-toggle="pill" href="#A-803L">A-803L</a></li>
    </ul>

    <div class="tab-content">
        @foreach($systems as $system)
            <div id="{{ $system }}" class="tab-pane fade">
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
        <div id="6X7-JO" class="tab-pane fade in active">
            <h3>Table Goes here</h3>
        </div>
        <div id="A-803L" class="tab-pane fade">
            <h3>Table Goes Here</h3>
        </div>
    </div>
</div>
@endsection