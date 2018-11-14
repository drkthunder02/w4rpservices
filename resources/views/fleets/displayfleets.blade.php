@extends('layouts.b4')
@section('content')
<div class="container">
    <h1>Work in Progress aka NOTHING WORKS!</h1><br>
    <h2>Fleets</h2>
    @for($i = 0; $i < $size; $i++)
        <a href="{{ route('addpilot', $data['fc'][$i], [Auth::user()->character_id]) }}">Join {{ $data['description'][$i] }}</a>
    @endfor
</div>
@endsection