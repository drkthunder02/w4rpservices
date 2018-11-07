@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Standing Fleet</h2>
    <a href="{{ route('/fleets/addpilot', ['id' => $charId]) }}">Join Standing Fleet</a>
    @if($fleetCommander == true)
    <a href="{{ route('/fleets/{id}/createwing', ['id' => $fleetId]) }}">Create Wing</a>
    <a href="{{ route('/fleets/{id}/createsquad', ['id' => $fleetId]) }}">Create Squad</a>
    @endif
    Display some other cool stuff about the fleet
</div>
@endsection