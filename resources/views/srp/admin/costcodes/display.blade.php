@extends('layouts.b4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>SRP Cost Codes</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Payout Percentage</th>
                    <th>Modify</th>
                </thead>
                <tbody>
                    @foreach($costcodes as $code)
                    <tr>
                        {!! Form::open(['action' => 'SRP\SRPAdminController@modifyCostCodes', 'method' => 'POST']) !!}
                        <td>
                            {{ $code['code'] }}
                            {{ Form::hidden('code', $code['code']) }}
                        </td>
                        <td>
                           {{ $code['description'] }}
                           {{ Form::text('description', null, ['class' => 'form-control', 'placeholder' => $code['description']]) }}
                        </td>
                        <td>
                            {{ $code['payout'] }}
                            {{ Form::text('payout', null, ['class' => 'form-control', 'placeholder' => $code['payout']]) }}
                        </td>
                        <td>
                            {{ Form::submit('Modify', ['class' => 'btn btn-primary']) }}
                        </td>
                    </tr>
                    {!! Form::close() !!}
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection