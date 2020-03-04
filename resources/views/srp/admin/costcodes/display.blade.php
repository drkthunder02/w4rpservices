@extends('srp.layouts.b4')
@section('content')
<div class="container">
    <table class="table table-striped">
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
                   {{ Form::text('description', null, ['class' => 'form-control', 'value' => $code['description']]) }}
                </td>
                <td>
                    {{ $code['payout'] }}
                    {{ Form::text('payout', null, ['class' => 'form-control', 'value' => $code['payout']]) }}
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
@endsection