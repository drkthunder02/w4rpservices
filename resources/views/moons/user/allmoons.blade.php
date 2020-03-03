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
                        <th>Availability</th>
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
@endsection