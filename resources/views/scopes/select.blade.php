@extends('layouts.b4');
@section('content')
<div class="container">
    <h2>Select Scopes for ESI</h2>
    {!! Form::open(['action' => 'EsiScopeController@redirectToProvider', 'method' => 'POST']) !!}
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Public Data') }}
            {{ Form::checkbox('scopes[]', 'publicData') }}
            {{ Form::label('scopes[]', 'Write Fleet') }}
            {{ Form::checkbox('scopes[]', 'esi-fleets.write_fleet.v1') }}
            {{ Form::label('scopes[]', 'Read Fleet') }}
            {{ Form::checkbox('scopes[]', 'esi-fleets.read_fleet.v1') }}
            {{ Form::label('scopes[]', 'Read Location') }}
            {{ Form::checkbox('scopes[]', 'esi-location.read_location.v1') }}
            {{ Form::label('scopes[]', 'Write Mail') }}
            {{ Form::checkbox('scopes[]', 'esi-mail.send_mail.v1') }}
            {{ Form::label('scopes[]', 'Read Mail') }}
            {{ Form::label('scopes[]', 'esi-mail.read_mail.v1')}}
            {{ Form::label('scopes[]', 'Corporation Wallets') }}
            {{ Form::checkbox('scopes[]', 'esi-wallet.read_corporation_wallets.v1') }}
            {{ Form::label('scopes[]', 'Read Structures') }}
            {{ Form::checkbox('scopes[]', 'esi-corporations.read_structures.v1') }}
            {{ Form::label('scopes[]', 'Structure Markets') }}
            {{ Form::checkbox('scopes[]', 'esi-markets.structure_markets.v1') }}
        </div>
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection