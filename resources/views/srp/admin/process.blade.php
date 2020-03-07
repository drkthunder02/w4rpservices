@extends('layouts.b4')
@section('content')

@if($requests != null)
    <div class="container col-md-12">
        <table class="table table-striped">
            <thead>
                <th>Timestamp</th>
                <th>Pilot</th>
                <th>FC</th>
                <th>zKillboard</th>
                <th>Total Loss</th>
                <th>Ship Type</th>
                <th>Payout %</th>
                <th>Fleet Type</th>
                <th>Actual SRP</th>
                <th>Notes</th>
                <th>Approve</th>
                <th>Deny</th>
                <th>Process</th>
            </thead>
            <tbody>
                
                @foreach($requests as $row)
                    {!! Form::open(['action' => 'SRP\SRPAdminController@processSRPRequest', 'method' => 'POST']) !!}
                    <tr>   
                        <td><!-- Timestamp -->
                            {{ Form::hidden('id', $row['id'], ['class' => 'form-control']) }}
                            {{ $row['created_at'] }}
                        </td>
                        <td><!-- Pilot -->
                            {{ $row['character_name'] }}
                        </td>
                        <td><!-- FC -->
                            {{ $row['fleet_commander_name'] }}
                        </td>
                        <td><!-- zkillboard link -->
                            <a href="{{ $row['zkillboard'] }}" target="_blank">zKill Link</a>
                        </td>
                        <td><!-- Total Loss -->
                            {{ Form::text('total_loss', number_format($row['loss_value'], 2, ".", ","), ['class' => 'form-control', 'id' => $row['id'], 'onchange' => 'UpdateTotalLoss(this.value,this.id)']) }}
                        </td>
                        <td><!-- Ship Type -->
                            {{ Form::select('ship_type', $viewShipTypes, $row['cost_code'], ['class' => 'form-control', 'onchange' => 'UpdateShipType(this.value)']) }}
                        </td>
                        <td><!-- Payout percentage -->
                            {{ $row['payout_percentage'] }}
                        </td>
                        <td><!-- Fleet Type -->
                            {{ $row['fleet_type'] }}
                        </td>
                        <td><!-- Actual SRP -->
                            {{ number_format($row['actual_srp'], 2, ".", ",") }}
                        </td>
                        <td><!-- Notes -->
                            {{ Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'notes', 'rows' => 2, 'cols' => 15, 'style' => 'resize:none']) }}
                        </td>
                        <td><!-- Approved -->
                            {{ Form::radio('approved', 'Approved', false, ['class' => 'form-control']) }}
                        </td>
                        <td><!-- Denied -->
                            {{ Form::radio('approved', 'Denied', false, ['class' => 'form-control']) }}
                        </td>
                        <td><!-- Update the row -->
                            {{ Form::submit('Process', ['class' => 'btn btn-primary']) }}
                        </td>
                    </tr>
                    {!! Form::close() !!}
                @endforeach
            </tbody>
        </table>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <h2>Totals</h2>
        </div>
        <div class="card-body">
            Total Loss Values: {{ $sum_loss }}<br>
            Total Actual Value: {{ $sum_actual }}<br>
        </div>
    </div>

    <script>
        function UpdateTotalLoss(val,id) {
            alert("The input value has changed. The new value is: " + val + " " + id);
        }

        function UpdateShipType(val) {
            alert("The input value for ship type has changed.  Thew new value is: " + val);
        }
    </script>
@else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>SRP Requests Dashboard</h2>
                </div>
                <div class="card-body">
                    <h3>No Open SRP Requests</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
        
@endsection