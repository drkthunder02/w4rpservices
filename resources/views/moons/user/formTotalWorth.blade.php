@extends('layouts.b4')
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
            'Brilliant Gneiss' => 'Brilliant Gneiss',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Cubic Bistot' => 'Cubic Bistot',
            'Dazzling Spodumain' => 'Dazzling Spodumain',
            'Euxenite' => 'Euxenite',
            'Flawless Arkonor' => 'Flawless Arkonor',
            'Glossy Scordite' => 'Glossy Scordite',
            'Immaculate Jaspet' => 'Immaculate Jaspet',
            'Jet Ochre' => 'Jet Ochre',
            'Loparite' => 'Loparite',
            'Lustrous Hedbergite' => 'Lustrous Hedbergite',
            'Monazite' => 'Monazite',
            'Opulent Pyroxeres' => 'Opulent Pyroxeres',
            'Otavite' => 'Otavite',
            'Pellucid Crokite' => 'Pellucid Crokite',
            'Platinoid Omber' => 'Platinoid Omber',
            'Pollucite' => 'Pollucite',
            'Resplendant Kernite' => 'Resplendant Kernite',
            'Scheelite' => 'Scheelite',
            'Scintillating Hemorphite' => 'Scintillating Hemorphite',
            'Sparkling Plagioclase' => 'Sparkling Plagioclase',
            'Sperrylite' => 'Sperrylite',
            'Stable Veldspar' => 'Stable Veldspar',
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
            'Brilliant Gneiss' => 'Brilliant Gneiss',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Cubic Bistot' => 'Cubic Bistot',
            'Dazzling Spodumain' => 'Dazzling Spodumain',
            'Euxenite' => 'Euxenite',
            'Flawless Arkonor' => 'Flawless Arkonor',
            'Glossy Scordite' => 'Glossy Scordite',
            'Immaculate Jaspet' => 'Immaculate Jaspet',
            'Jet Ochre' => 'Jet Ochre',
            'Loparite' => 'Loparite',
            'Lustrous Hedbergite' => 'Lustrous Hedbergite',
            'Monazite' => 'Monazite',
            'Opulent Pyroxeres' => 'Opulent Pyroxeres',
            'Otavite' => 'Otavite',
            'Pellucid Crokite' => 'Pellucid Crokite',
            'Platinoid Omber' => 'Platinoid Omber',
            'Pollucite' => 'Pollucite',
            'Resplendant Kernite' => 'Resplendant Kernite',
            'Scheelite' => 'Scheelite',
            'Scintillating Hemorphite' => 'Scintillating Hemorphite',
            'Sparkling Plagioclase' => 'Sparkling Plagioclase',
            'Sperrylite' => 'Sperrylite',
            'Stable Veldspar' => 'Stable Veldspar',
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
            'Brilliant Gneiss' => 'Brilliant Gneiss',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Cubic Bistot' => 'Cubic Bistot',
            'Dazzling Spodumain' => 'Dazzling Spodumain',
            'Euxenite' => 'Euxenite',
            'Flawless Arkonor' => 'Flawless Arkonor',
            'Glossy Scordite' => 'Glossy Scordite',
            'Immaculate Jaspet' => 'Immaculate Jaspet',
            'Jet Ochre' => 'Jet Ochre',
            'Loparite' => 'Loparite',
            'Lustrous Hedbergite' => 'Lustrous Hedbergite',
            'Monazite' => 'Monazite',
            'Opulent Pyroxeres' => 'Opulent Pyroxeres',
            'Otavite' => 'Otavite',
            'Pellucid Crokite' => 'Pellucid Crokite',
            'Platinoid Omber' => 'Platinoid Omber',
            'Pollucite' => 'Pollucite',
            'Resplendant Kernite' => 'Resplendant Kernite',
            'Scheelite' => 'Scheelite',
            'Scintillating Hemorphite' => 'Scintillating Hemorphite',
            'Sparkling Plagioclase' => 'Sparkling Plagioclase',
            'Sperrylite' => 'Sperrylite',
            'Stable Veldspar' => 'Stable Veldspar',
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
            'Brilliant Gneiss' => 'Brilliant Gneiss',
            'Carnotite' => 'Carnotite',
            'Chromite' => 'Chromite',
            'Cinnabar' => 'Cinnabar',
            'Cobaltite' => 'Cobaltite',
            'Coesite' => 'Coesite',
            'Cubic Bistot' => 'Cubic Bistot',
            'Dazzling Spodumain' => 'Dazzling Spodumain',
            'Euxenite' => 'Euxenite',
            'Flawless Arkonor' => 'Flawless Arkonor',
            'Glossy Scordite' => 'Glossy Scordite',
            'Immaculate Jaspet' => 'Immaculate Jaspet',
            'Jet Ochre' => 'Jet Ochre',
            'Loparite' => 'Loparite',
            'Lustrous Hedbergite' => 'Lustrous Hedbergite',
            'Monazite' => 'Monazite',
            'Opulent Pyroxeres' => 'Opulent Pyroxeres',
            'Otavite' => 'Otavite',
            'Pellucid Crokite' => 'Pellucid Crokite',
            'Platinoid Omber' => 'Platinoid Omber',
            'Pollucite' => 'Pollucite',
            'Resplendant Kernite' => 'Resplendant Kernite',
            'Scheelite' => 'Scheelite',
            'Scintillating Hemorphite' => 'Scintillating Hemorphite',
            'Sparkling Plagioclase' => 'Sparkling Plagioclase',
            'Sperrylite' => 'Sperrylite',
            'Stable Veldspar' => 'Stable Veldspar',
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
    <div class="form-group col-md-1">
        {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
        {!! Form::close() !!}
    </div>
</div>
@endsection
