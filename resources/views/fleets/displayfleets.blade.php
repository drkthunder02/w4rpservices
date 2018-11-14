@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Fleets</h2>
    @for($i = 0; $i < count($data[0]); $i++)
        <a href="{{ route('addpilot', [$data[1][$i], Auth::user()->character_id]) }}">Join {{ $data[2][$i] }}</a><br>
        @if(Auth::user()->character_id == $data[0][$i])
        <a href="{{ route('deletefleet', [data[0][$i]]) }}">Delete Fleet</a><br><br>
        @endif
    @endfor
</div>
@endsection