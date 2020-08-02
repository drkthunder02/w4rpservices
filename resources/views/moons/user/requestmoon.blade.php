@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Moon Reservation Request Form</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'Moons\MoonsController@storeRequestAllianceMoon', 'method' => 'POST']) !!}
            <div class="form-group">
                {{ Form::label('system', 'System') }}
                {{ Form::select('system', [
                    '4-GB14' => '4-GB14',
                    '6X7-JO' => '6X7-JO',
                    '78TS-Q' => '78TS-Q',
                    'AF0-V5' => 'AF0-V5',
                    'A4B-V5' => 'A4B-V5',
                    'A803-L' => 'A803-L',
                    'B-A587' => 'B-A587',
                    'B-KDOZ' => 'B-KDOZ',
                    'B-S347' => 'B-S347',
                    'B9E-H6' => 'B9E-H6',
                    'CJNF-J' => 'CJNF-J',
                    'DY-P7Q' => 'DY-P7Q',
                    'E8-YS9' => 'E8-YS9',
                    'EA-HSA' => 'EA-HSA',
                    'FYI-49' => 'FYI-49',
                    'GJ0-JO' => 'GJ0-JO',
                    'GXK-7F' => 'GXK-7F',
                    'I8-D0G' => 'I8-D0G',
                    'J-ODE7' => 'J-ODE7',
                    'JDAS-0' => 'JDAS-0',
                    'JWZ2-V' => 'JWZ2-V',
                    'L-5JCJ' => 'L-5JCJ',
                    'LK1K-5' => 'LK1K-5',
                    'LN-56V' => 'LN-56V',
                    'NS2L-4' => 'NS2L-4',
                    'O7-7UX' => 'O7-7UX',
                    'OGL8-Q' => 'OGL8-Q',
                    'PPFB-U' => 'PPFB-U',
                    'Q-S7ZD' => 'Q-S7ZD',
                    'QE-E1D' => 'QE-E1D',
                    'QI-S9W' => 'QI-S9W',
                    'R-K4QY' => 'R-K4QY',
                    'REB-KR' => 'REB-KR',
                    'SPBS-6' => 'SPBS-6',
                    'WQH-4K' => 'WQH-4K',
                    'WYF8-8' => 'WYF8-8',
                    'XVV-21' => 'XVV-21',
                    'Y19P-1' => 'Y19P-1',
                    'Y2-QUV' => 'Y2-QUV',
                    'Z-H2MA' => 'Z-H2MA',
                    'ZBP-TP' => 'ZBP-TP',
                ], 'None') }}
            </div>
            <div class="form-group">
                {{ Form::label('planet', 'Planet') }}
                {{ Form::text('planet', '', ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('moon', 'Moon') }}
                {{ Form::text('moon', '', ['class' => 'form-control']) }}
            </div>
            {{ Form::submit('Submit Request', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection