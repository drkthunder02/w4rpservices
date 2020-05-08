@extends('layouts.user.dashb4')
@section('content')
<div class="container">
    <h2>Total Worth Calculator</h2><br>
    <h3>Enter the moon composition</h3><br>
    {!! Form::open(['action' => 'Moons\MoonsController@displayTotalWorth', 'method' => 'POST']) !!}
    <div class="form-group col-md-6">
        {{ Form::label('firstOre', 'First Ore') }}
        {{ Form::select('firstOre', [
            'None' => 'None',
            'Bitumens' => 'Bitumens',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Euxenite' => 'Euxenite',
            'Loparite' => 'Loparite',
            'Monazite' => 'Monazite',
            'Otavite' => 'Otavite',
            'Pollucite' => 'Pollucite',
            'Scheelite' => 'Scheelite',
            'Sperrylite' => 'Sperrylite',
            'Sylvite' => 'Sylvite',
            'Titanite' => 'Titanite',
            'Vanadinite' => 'Vanadinite',
            'Xenotime' => 'Xenotime',
            'Ytterbite' => 'Ytterbite',
            'Zeolites' => 'Zeolites',
            'Zircon' => 'Zircon',
        ], 'None') }}
        {{ Form::text('firstQuantity', '', ['class' => 'form-control']) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('secondOre', 'Second Ore') }}
        {{ Form::select('secondOre', [
            'None' => 'None',
            'Bitumens' => 'Bitumens',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Euxenite' => 'Euxenite',
            'Loparite' => 'Loparite',
            'Monazite' => 'Monazite',
            'Otavite' => 'Otavite',
            'Pollucite' => 'Pollucite',
            'Scheelite' => 'Scheelite',
            'Sperrylite' => 'Sperrylite',
            'Sylvite' => 'Sylvite',
            'Titanite' => 'Titanite',
            'Vanadinite' => 'Vanadinite',
            'Xenotime' => 'Xenotime',
            'Ytterbite' => 'Ytterbite',
            'Zeolites' => 'Zeolites',
            'Zircon' => 'Zircon',
        ], 'None') }}
        {{ Form::text('secondQuantity', '', ['class' => 'form-control']) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('thirdOre', 'Third Ore') }}
        {{ Form::select('thirdOre', [
            'None' => 'None',
            'Bitumens' => 'Bitumens',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Euxenite' => 'Euxenite',
            'Loparite' => 'Loparite',
            'Monazite' => 'Monazite',
            'Otavite' => 'Otavite',
            'Pollucite' => 'Pollucite',
            'Scheelite' => 'Scheelite',
            'Sperrylite' => 'Sperrylite',
            'Sylvite' => 'Sylvite',
            'Titanite' => 'Titanite',
            'Vanadinite' => 'Vanadinite',
            'Xenotime' => 'Xenotime',
            'Ytterbite' => 'Ytterbite',
            'Zeolites' => 'Zeolites',
            'Zircon' => 'Zircon',
        ], 'None') }}
        {{ Form::text('thirdQuantity', '', ['class' => 'form-control']) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('fourthOre', 'Fourth Ore') }}
        {{ Form::select('fourthOre', [
            'None' => 'None',
            'Bitumens' => 'Bitumens',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Euxenite' => 'Euxenite',
            'Loparite' => 'Loparite',
            'Monazite' => 'Monazite',
            'Otavite' => 'Otavite',
            'Pollucite' => 'Pollucite',
            'Scheelite' => 'Scheelite',
            'Sperrylite' => 'Sperrylite',
            'Sylvite' => 'Sylvite',
            'Titanite' => 'Titanite',
            'Vanadinite' => 'Vanadinite',
            'Xenotime' => 'Xenotime',
            'Ytterbite' => 'Ytterbite',
            'Zeolites' => 'Zeolites',
            'Zircon' => 'Zircon',
        ], 'None') }}
        {{ Form::text('fourthQuantity', '', ['class' => 'form-control']) }}
    </div>
    <div class="form-group col-md-2">
        {{ Form::label('reprocessing', 'Reprocessing') }}
        {{ Form::text('reprocessing', '', ['class' => 'form-control', 'placeholder' => '0.84']) }}
    </div>
    <div class="form-group col-md-1">
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
        {!! Form::close() !!}
    </div>
</div>
@endsection
