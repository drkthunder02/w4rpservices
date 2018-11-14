@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Fleets</h2>
    @if(isset($error))
        <?php var_dump($error); ?>
    @endif
    @if(isset($data))
    @for($i = 0; $i < count($data[0]); $i++)
        <a href="{{ route('addpilot', [$data[1][$i], Auth::user()->character_id]) }}">Join {{ $data[2][$i] }}</a><br>
        @if(Auth::user()->character_id == $data[0][$i])
        <a href="{{ route('deletefleet', [$data[1][$i]]) }}">Delete Fleet</a><br><br>
        @endif
    @endfor
    @endif
</div>
@endsection