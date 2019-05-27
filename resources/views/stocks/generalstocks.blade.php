@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="row">
        <div table="table table-striped table-bordered table-hover">
            <thead>
                <th>Location</th>
                <th>Items</th>
                <th>Quantities</th>
                <th>Date</th>
            </thead>
            <tbody>
                @foreach()
                    <tr data-toggle="collapse" data-target="{{ $stationName }}" class="accordion-toggle">
                        <td>Station</td>
                        <td>Item Types</td>
                        <td></td>
                        <td>Date</td>
                    </tr>
                    @foreach()
                    <tr class="hiddenRow">
                        <td>
                            <div class="accordion-body collapse" id="{{ $stationName }}"></div>
                        </td>
                        <td>
                            <div class="accordion-body collapse" id="{{ $stationName }}"></div>
                        </td>
                        <td>
                            <div class="accordion-body collapse" id="{{ $stationName }}"></div>
                        </td>
                        <td>
                            <div class="accordion-body collapse" id="{{ $stationName }}"></div>
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </div>
    </div>
</div>
@endsection
