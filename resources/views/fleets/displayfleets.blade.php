@extends('layouts.b4')
@section('content')
<div class="container">
    <h1>Work in Progress aka NOTHING WORKS!</h1><br>
    <h2>Fleets</h2>
    @foreach ($fleetIds as $fleet)
        <a href="{{ route('addpilot', $fleet['fleet'], [Auth::user()->character_id]) }}">Join {{ $fleet['description'] }}</a>
    @endforeach
</div>
@endsection