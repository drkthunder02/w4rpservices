@extends('layouts.b4');
@section('content')
<?php
    $publicData = false;
    $writeFleet = false;
    $readFleet = false;
    $readLocation = false;
    $writeMail = false;
    $readMail = false;
    $readCorpWallets = false;
    $readStructures = false;
    $structureMarkets = false;
    $corpAssets = false;
    $universeStructures = false;
?>
<div class="container">
    <h2>Select Scopes for ESI</h2>
    {!! Form::open(['action' => 'EsiScopeController@redirectToProvider', 'method' => 'POST']) !!}
        @foreach($scopes as $scope)
            @if($scope->scope == 'publicData')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Public Data') }}
                    {{ Form::checkbox('scopes[]', 'publicData', 'true') }}
                </div>
                <?php $publicData = true; ?>
                @break
            @endif
        @endforeach
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-fleets.write_fleet.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Write Fleet') }}
                    {{ Form::checkbox('scopes[]', 'esi-fleets.write_fleet.v1', 'true') }}
                </div>
                <?php $writeFleet = true; ?>
            @break
            @endif
        @endforeach        
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-fleets.read_fleet.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Read Fleet') }}
                    {{ Form::checkbox('scopes[]', 'esi-fleets.read_fleet.v1', 'true') }}
                </div>
                <?php $readFleet = true; ?>
            @break
            @endif
        @endforeach        
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-location.read_location.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Read Location') }}
                    {{ Form::checkbox('scopes[]', 'esi-location.read_location.v1', 'true') }}
                </div>
                <?php $readLocation = true; ?>
            @break
            @endif
        @endforeach
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-mail.send_mail.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Write Mail') }}
                    {{ Form::checkbox('scopes[]', 'esi-mail.send_mail.v1', 'true') }}
                </div>
                <?php $sendMail = true; ?>
            @break
            @endif
        @endforeach        
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-mail.read_mail.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Read Mail') }}
                    {{ Form::checkbox('scopes[]', 'esi-mail.read_mail.v1', 'true')}}
                </div>
                <?php $readMail = true; ?>
            @break
            @endif
        @endforeach        
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-wallet.read_corporation_wallets.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Corporation Wallets') }}
                    {{ Form::checkbox('scopes[]', 'esi-wallet.read_corporation_wallets.v1', 'true') }}
                </div>
                <?php $readCorpWallets = true; ?>
            @break
            @endif
        @endforeach
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-corporations.read_structures.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Read Structures') }}
                    {{ Form::checkbox('scopes[]', 'esi-corporations.read_structures.v1', 'true') }}
                </div>
                <?php $readStructures = true; ?>
            @break
            @endif
        @endforeach
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-markets.structure_markets.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Structure Markets') }}
                    {{ Form::checkbox('scopes[]', 'esi-markets.structure_markets.v1', 'true') }}
                </div>
                <?php $structureMarkets = true; ?>
            @break
            @endif
        @endforeach
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-assets.read_corporation_assets.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Corporation Assets') }}
                    {{ Form::checkbox('scopes[]', 'esi-assets.read_corporation_assets.v1', 'true') }}
                </div>
            @endif
        @endforeach
        @foreach($scopes as $scope)
            @if($scope->scope == 'esi-universe.read_structures.v1')
                <div class="form-group col-md-6">
                    {{ Form::label('scopes[]', 'Corporation Assets') }}
                    {{ Form::checkbox('scopes[]', 'esi-universe.read_structures.v1', 'true') }}
                </div>
            @endif
        @endforeach

        @if($publicData == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Public Data') }}
            {{ Form::checkbox('scopes[]', 'publicData') }}
        </div>
        @endif
        @if($writeFleet == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Write Fleet') }}
            {{ Form::checkbox('scopes[]', 'esi-fleets.write_fleet.v1') }}
        </div>
        @endif
        @if($readFleet == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Read Fleet') }}
            {{ Form::checkbox('scopes[]', 'esi-fleets.read_fleet.v1') }}
        </div>
        @endif
        @if($readLocation == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Read Location') }}
            {{ Form::checkbox('scopes[]', 'esi-location.read_location.v1') }}
        </div>
        @endif
        @if($writeMail == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Write Mail') }}
            {{ Form::checkbox('scopes[]', 'esi-mail.send_mail.v1') }}
        </div>
        @endif
        @if($readMail == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Read Mail') }}
            {{ Form::checkbox('scopes[]', 'esi-mail.read_mail.v1')}}
        </div>
        @endif
        @if($readCorpWallets == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Corporation Wallets') }}
            {{ Form::checkbox('scopes[]', 'esi-wallet.read_corporation_wallets.v1') }}
        </div>
        @endif
        @if($readStructures == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Read Structures') }}
            {{ Form::checkbox('scopes[]', 'esi-corporations.read_structures.v1') }}
        </div>
        @endif
        @if($structureMarkets == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Structure Markets') }}
            {{ Form::checkbox('scopes[]', 'esi-markets.structure_markets.v1') }}
        </div>
        @endif
        @if($corpAssets == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Corporation Assets') }}
            {{ Form::checkbox('scopes[]', 'esi-assets.read_corporation_assets.v1') }}
        </div>
        @endif
        @if($universeStructures == false)
        <div class="form-group col-md-6">
            {{ Form::label('scopes[]', 'Universe Structures') }}
            {{ Form::checkbox('scopes[]', 'esi-universe.read_structures.v1') }}
        </div>
        @endif
        
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
    {!! Form::close() !!}
</div>
@endsection