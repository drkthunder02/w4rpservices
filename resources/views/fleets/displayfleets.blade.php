@extends('layouts.b4')
@section('content')
<div class="container">
    <h1>Work in Progress aka NOTHING WORKS!</h1><br>
    <h2>Standing Fleet</h2>
    <a href="{{ route('addpilot', [Auth::user()->character_id]) }}">Join Standing Fleet</a>
    Display some other cool stuff about the fleet
</div>
@endsection